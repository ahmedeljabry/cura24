<?php

namespace App\Services;

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;

class GoogleTranslateService
{
    private const OAUTH_SCOPE = 'https://www.googleapis.com/auth/cloud-translation';

    private ?Client $client = null;
    private ?Client $basicClient = null;

    public function translatePayload(array $data, array $paths): array
    {
        if (!$this->isEnabled() || empty($paths)) {
            return $data;
        }

        $groupedValues = [
            'text/plain' => [],
            'text/html' => [],
        ];

        foreach ($paths as $path => $format) {
            $mimeType = $format === 'html' ? 'text/html' : 'text/plain';

            foreach ($this->collectMatches($data, explode('.', $path)) as $matchedPath => $value) {
                if (!$this->shouldTranslateValue($value)) {
                    continue;
                }

                $groupedValues[$mimeType][$matchedPath] = $value;
            }
        }

        foreach ($groupedValues as $mimeType => $values) {
            if (empty($values)) {
                continue;
            }

            foreach ($this->translateMap($values, $mimeType) as $path => $translatedValue) {
                Arr::set($data, $path, $translatedValue);
            }
        }

        return $data;
    }

    public function isEnabled(): bool
    {
        return (bool) config('services.google_translate.enabled')
            && ($this->usesApiKey() || filled(config('services.google_translate.project_id')));
    }

    protected function translateMap(array $values, string $mimeType): array
    {
        try {
            $translations = $this->requestTranslations(array_values($values), $mimeType);
        } catch (Throwable $exception) {
            report($exception);

            return $values;
        }

        if (count($translations) !== count($values)) {
            return $values;
        }

        $translatedValues = [];
        $index = 0;

        foreach ($values as $path => $originalValue) {
            $translatedValues[$path] = $this->resolveTranslatedValue(
                $originalValue,
                $translations[$index] ?? []
            );
            $index++;
        }

        return $translatedValues;
    }

    protected function requestTranslations(array $contents, string $mimeType): array
    {
        if ($this->usesApiKey()) {
            return $this->requestBasicTranslations($contents, $mimeType);
        }

        $response = $this->client()->post($this->endpoint(), [
            'json' => [
                'contents' => $contents,
                'targetLanguageCode' => config('services.google_translate.target_language', 'it'),
                'mimeType' => $mimeType,
            ],
        ]);

        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $payload['translations'] ?? [];
    }

    protected function requestBasicTranslations(array $contents, string $mimeType): array
    {
        $response = $this->basicClient()->post('language/translate/v2', [
            'query' => [
                'key' => config('services.google_translate.api_key'),
            ],
            'json' => [
                'q' => $contents,
                'target' => config('services.google_translate.target_language', 'it'),
                'format' => $mimeType === 'text/html' ? 'html' : 'text',
            ],
        ]);

        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return array_map(function (array $translation): array {
            return [
                'translatedText' => $translation['translatedText'] ?? null,
                'detectedLanguageCode' => $translation['detectedSourceLanguage'] ?? null,
            ];
        }, $payload['data']['translations'] ?? []);
    }

    protected function client(): Client
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $credentialsPath = config('services.google_translate.credentials');

        if (filled($credentialsPath)) {
            putenv(sprintf('GOOGLE_APPLICATION_CREDENTIALS=%s', $credentialsPath));
        }

        $stack = HandlerStack::create();
        $stack->push(ApplicationDefaultCredentials::getMiddleware([self::OAUTH_SCOPE]));

        return $this->client = new Client([
            'handler' => $stack,
            'base_uri' => 'https://translate.googleapis.com',
            'auth' => 'google_auth',
            'timeout' => (float) config('services.google_translate.timeout', 10),
            'http_errors' => true,
        ]);
    }

    protected function basicClient(): Client
    {
        if ($this->basicClient !== null) {
            return $this->basicClient;
        }

        return $this->basicClient = new Client([
            'base_uri' => 'https://translation.googleapis.com/',
            'timeout' => (float) config('services.google_translate.timeout', 10),
            'http_errors' => true,
        ]);
    }

    protected function endpoint(): string
    {
        return sprintf('v3/%s:translateText', $this->parent());
    }

    protected function parent(): string
    {
        return sprintf(
            'projects/%s/locations/%s',
            config('services.google_translate.project_id'),
            config('services.google_translate.location', 'global')
        );
    }

    protected function resolveTranslatedValue(string $originalValue, array $translation): string
    {
        $detectedLanguageCode = Str::lower((string) ($translation['detectedLanguageCode'] ?? ''));

        if ($this->isTargetLanguage($detectedLanguageCode)) {
            return $originalValue;
        }

        $translatedValue = (string) ($translation['translatedText'] ?? $originalValue);

        if ($translatedValue === '') {
            return $originalValue;
        }

        return html_entity_decode($translatedValue, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function shouldTranslateValue(mixed $value): bool
    {
        return is_string($value) && trim(strip_tags($value)) !== '';
    }

    protected function collectMatches(mixed $value, array $segments, string $currentPath = ''): array
    {
        if ($segments === []) {
            return $currentPath === '' ? [] : [$currentPath => $value];
        }

        $segment = array_shift($segments);

        if ($segment === '*') {
            if (!is_array($value)) {
                return [];
            }

            $matches = [];

            foreach ($value as $key => $nestedValue) {
                $path = $currentPath === '' ? (string) $key : $currentPath . '.' . $key;
                $matches += $this->collectMatches($nestedValue, $segments, $path);
            }

            return $matches;
        }

        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return [];
        }

        $path = $currentPath === '' ? $segment : $currentPath . '.' . $segment;

        return $this->collectMatches($value[$segment], $segments, $path);
    }

    protected function isTargetLanguage(string $languageCode): bool
    {
        $targetLanguage = Str::lower((string) config('services.google_translate.target_language', 'it'));

        return $languageCode !== '' && Str::startsWith($languageCode, $targetLanguage);
    }

    protected function usesApiKey(): bool
    {
        return filled(config('services.google_translate.api_key'));
    }
}
