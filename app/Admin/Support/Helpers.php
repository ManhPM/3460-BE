
<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

if (! function_exists('isSuperAdmin')) {
    function isSuperAdmin()
    {
        return auth()->user()->hasRole('superAdmin');
    }
}

if (! function_exists('generate_text_depth_tree')) {
    /**
     * Tạo text theo độ sâu.
     *
     * @param integer $depth
     */
    function generate_text_depth_tree($depth, $word = '-')
    {
        $text = '';
        if ($depth > 0) {
            for ($i = 0; $i < $depth; $i++) {
                $text .= $word;
            }
        }
        return $text;
    }
}
if (! function_exists('uniqid_real')) {
    function uniqid_real($lenght = 13)
    {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new \Exception("no cryptographically secure random function available");
        }
        return Str::upper(substr(bin2hex($bytes), 0, $lenght));
    }
}

if (! function_exists('format_price')) {
    function format_price($price, $positionCurrent = 0)
    {
        if ($positionCurrent == 'left') {
            return config('custom.currency') . number_format($price);
        } else {
            return number_format($price) . ' ' . config('custom.currency');
        }
    }
}

if (! function_exists('format_price_no_currency')) {
    function format_price_no_currency($price)
    {
        return number_format($price);
    }
}

if (! function_exists('format_point')) {
    function format_point($point)
    {
        return number_format($point);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date or return an empty string if the input is null.
     *
     * @param string|\DateTimeInterface|null $date
     * @param string $format
     * @return string
     */
    function format_date($date, $format = 'd-m-Y'): string
    {
        if ($date === null) {
            return '';
        }

        if (is_string($date)) {
            try {
                $date = new \DateTime($date);
            } catch (\Exception $e) {
                return ''; // Return empty string if the date string is invalid
            }
        }

        return $date->format($format);
    }
}


if (!function_exists('format_date_user')) {
    /**
     * Format a date or return an empty string if the input is null.
     *
     * @param string|\DateTimeInterface|null $date
     * @param string $format
     * @return string
     */
    function format_date_user($date, $format = 'd-m-Y'): string
    {
        if ($date === null) {
            return '';
        }

        if (is_string($date)) {
            try {
                $date = new \DateTime($date);
            } catch (\Exception $e) {
                return ''; // Return empty string if the date string is invalid
            }
        }

        return $date->format($format);
    }
}


if (!function_exists('format_datetime')) {
    function format_datetime($datetime, $format = null): ?string
    {
        if ($datetime) {
            $format = $format ?: config('custom.format.datetime');
            return date($format, strtotime($datetime));
        }
        return null;
    }
}


if (!function_exists('format_time')) {
    /**
     * Formats the time portion of a datetime string.
     *
     * @param string|null $datetime The datetime string to format.
     * @param string|null $format The time format to use, defaults to a configuration or 'H:i:s'.
     * @return string|null Formatted time or null if input is null.
     */
    function format_time($datetime, $format = null): ?string
    {
        if ($datetime) {
            // Set the default format from configuration or use 'H:i:s' if not configured
            $format = $format ?: config('custom.format.time', 'H:i:s');
            return date($format, strtotime($datetime));
        }
        return null;
    }
}

if (!function_exists('getBoundsByName')) {
    /**
     * Lấy khung giới hạn cho một địa điểm cụ thể bằng cách sử dụng Google Geocoding API.
     *
     * @param string $name Tên địa điểm cần truy vấn.
     * @return array|null Mảng khung giới hạn hoặc null nếu không tìm thấy.
     */
    function getBoundsByName(string $name): ?array
    {
        $apiKey = config('services.google_maps.api_key');
        $encodedName = urlencode($name);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedName}&key={$apiKey}";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['results']) && isset($data['results'][0]['geometry']['bounds'])) {
                return $data['results'][0]['geometry']['bounds'];
            } else {
                return null;
            }
        }

        return null;
    }
}
