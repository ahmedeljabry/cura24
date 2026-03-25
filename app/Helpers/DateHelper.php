<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

if (!function_exists('format_datetime')) {
    /**
     * Format a date/time using configured static options for date format, time format, and timezone.
     *
     * @param mixed $date The date to format (string, Carbon instance, DateTime, etc.)
     * @param bool $includeTime Whether to include time in the output
     * @param string|null $fallbackDateFormat Fallback date format if not set in static options
     * @param string|null $fallbackTimeFormat Fallback time format if not set in static options
     * @return string Formatted date/time string or original input on failure
     */
    function format_datetime(
        $date,
        bool $includeTime = false,
        ?string $fallbackDateFormat = 'Y-m-d',
        ?string $fallbackTimeFormat = 'H:i'
    ): string
    {
        // Retrieve static options
        $timezone = get_static_option('timezone') ?? config('app.timezone', 'UTC');
        $dateFormat = get_static_option('date_format') ?? $fallbackDateFormat;
        $timeFormatOption = get_static_option('time_format') ?? '24';

        // Determine time format based on 12/24-hour preference
        $timeFormat = $timeFormatOption === '12' ? 'h:i A' : $fallbackTimeFormat;

        // Combine date and time formats if time is included
        $format = $includeTime ? "$dateFormat $timeFormat" : $dateFormat;

        try {
            // Parse the date and apply timezone
            $carbonDate = Carbon::parse($date)->setTimezone($timezone);

            // Return formatted date/time
            return $carbonDate->format($format);
        } catch (\Exception $e) {
            // Log the error for debugging (optional)
            Log::warning('Invalid date format in format_datetime: ' . $e->getMessage(), [
                'date' => $date,
                'format' => $format,
            ]);

            // Return original input as fallback
            return is_string($date) ? $date : '';
        }
    }
}

if (!function_exists('format_date')) {
    /**
     * Format only the date (wrapper for format_datetime).
     *
     * @param mixed $date The date to format
     * @param string|null $fallbackDateFormat Fallback date format
     * @return string Formatted date string
     */
    function format_date($date, ?string $fallbackDateFormat = 'Y-m-d'): string
    {
        return format_datetime($date, false, $fallbackDateFormat);
    }
}

if (!function_exists('format_time')) {
    /**
     * Format only the time (wrapper for format_datetime).
     *
     * @param mixed $date The date to format
     * @param string|null $fallbackTimeFormat Fallback time format
     * @return string Formatted time string
     */
    function format_time($date, ?string $fallbackTimeFormat = 'H:i'): string
    {
        // Retrieve static time format option
        $timeFormatOption = get_static_option('time_format') ?? '24';
        $timeFormat = $timeFormatOption === '12' ? 'h:i A' : $fallbackTimeFormat;

        try {
            $timezone = get_static_option('timezone') ?? config('app.timezone', 'UTC');
            $carbonDate = Carbon::parse($date)->setTimezone($timezone);

            return $carbonDate->format($timeFormat);
        } catch (\Exception $e) {
            Log::warning('Invalid time format in format_time: ' . $e->getMessage(), [
                'date' => $date,
                'format' => $timeFormat,
            ]);

            return is_string($date) ? $date : '';
        }
    }
}