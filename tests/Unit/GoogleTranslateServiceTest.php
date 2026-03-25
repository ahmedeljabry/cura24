<?php

namespace Tests\Unit;

use App\Services\GoogleTranslateService;
use Tests\TestCase;

class GoogleTranslateServiceTest extends TestCase
{
    public function test_it_returns_original_payload_when_translation_is_disabled(): void
    {
        config([
            'services.google_translate.enabled' => false,
            'services.google_translate.project_id' => 'demo-project',
        ]);

        $service = new GoogleTranslateService();
        $payload = [
            'title' => 'Hello',
            'description' => '<p>Hello world</p>',
        ];

        $translated = $service->translatePayload($payload, [
            'title' => 'text',
            'description' => 'html',
        ]);

        $this->assertSame($payload, $translated);
    }

    public function test_it_translates_only_non_italian_values(): void
    {
        config([
            'services.google_translate.enabled' => true,
            'services.google_translate.project_id' => 'demo-project',
            'services.google_translate.target_language' => 'it',
        ]);

        $service = new class extends GoogleTranslateService {
            protected function requestTranslations(array $contents, string $mimeType): array
            {
                if ($mimeType === 'text/plain') {
                    return [
                        [
                            'translatedText' => 'Ciao',
                            'detectedLanguageCode' => 'en',
                        ],
                        [
                            'translatedText' => 'Pulizia',
                            'detectedLanguageCode' => 'it',
                        ],
                        [
                            'translatedText' => 'Domanda frequente',
                            'detectedLanguageCode' => 'en',
                        ],
                    ];
                }

                return [
                    [
                        'translatedText' => '<p>Ciao mondo</p>',
                        'detectedLanguageCode' => 'en',
                    ],
                ];
            }
        };

        $translated = $service->translatePayload([
            'title' => 'Hello',
            'description' => '<p>Hello world</p>',
            'include_service_title' => [
                1 => 'Pulizia',
            ],
            'faqs_title' => [
                0 => 'Common question',
            ],
        ], [
            'title' => 'text',
            'description' => 'html',
            'include_service_title.1' => 'text',
            'faqs_title.0' => 'text',
        ]);

        $this->assertSame('Ciao', $translated['title']);
        $this->assertSame('<p>Ciao mondo</p>', $translated['description']);
        $this->assertSame('Pulizia', $translated['include_service_title'][1]);
        $this->assertSame('Domanda frequente', $translated['faqs_title'][0]);
    }

    public function test_it_translates_wildcard_paths(): void
    {
        config([
            'services.google_translate.enabled' => true,
            'services.google_translate.project_id' => 'demo-project',
            'services.google_translate.target_language' => 'it',
        ]);

        $service = new class extends GoogleTranslateService {
            protected function requestTranslations(array $contents, string $mimeType): array
            {
                return [
                    [
                        'translatedText' => 'Servizio di base',
                        'detectedLanguageCode' => 'en',
                    ],
                    [
                        'translatedText' => 'Servizio premium',
                        'detectedLanguageCode' => 'en',
                    ],
                ];
            }
        };

        $translated = $service->translatePayload([
            'include_service_inputs' => [
                ['include_service_title' => 'Basic service'],
                ['include_service_title' => 'Premium service'],
            ],
        ], [
            'include_service_inputs.*.include_service_title' => 'text',
        ]);

        $this->assertSame('Servizio di base', $translated['include_service_inputs'][0]['include_service_title']);
        $this->assertSame('Servizio premium', $translated['include_service_inputs'][1]['include_service_title']);
    }

    public function test_it_supports_api_key_authentication_without_project_id(): void
    {
        config([
            'services.google_translate.enabled' => true,
            'services.google_translate.project_id' => null,
            'services.google_translate.api_key' => 'demo-api-key',
            'services.google_translate.target_language' => 'it',
        ]);

        $service = new class extends GoogleTranslateService {
            protected function requestTranslations(array $contents, string $mimeType): array
            {
                return [
                    [
                        'translatedText' => 'Ciao',
                        'detectedLanguageCode' => 'en',
                    ],
                ];
            }
        };

        $translated = $service->translatePayload([
            'title' => 'Hello',
        ], [
            'title' => 'text',
        ]);

        $this->assertSame('Ciao', $translated['title']);
    }
}
