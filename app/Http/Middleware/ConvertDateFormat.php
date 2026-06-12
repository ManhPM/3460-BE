<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class ConvertDateFormat
{
    public function handle($request, Closure $next)
    {
        $data = $request->all();
        $converted = $this->convertDatesInArray($data);
        $request->merge($converted);

        return $next($request);
    }

    private function convertDatesInArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Đệ quy tiếp nếu là mảng con
                $data[$key] = $this->convertDatesInArray($value);
            } elseif (is_string($value)) {
                try {
                    if (preg_match('/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}$/', $value)) {
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $value);
                        $data[$key] = $date->format('Y-m-d H:i:s');
                    } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $value);
                        $data[$key] = $date->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    // Nếu lỗi format thì bỏ qua, không thay đổi
                }
            }
        }

        return $data;
    }
}
