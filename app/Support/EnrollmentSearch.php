<?php

namespace App\Support;

use Illuminate\Support\Collection;

class EnrollmentSearch
{
    public static function filter(Collection $rows, ?string $query): Collection
    {
        $q = trim((string) $query);
        if ($q === '') {
            return $rows;
        }

        $needle = mb_strtolower($q);
        $digits = preg_replace('/\D+/', '', $q);
        $idOnly = ltrim($q, '#');

        return $rows->filter(function ($e) use ($needle, $digits, $idOnly) {
            if (ctype_digit($idOnly) && (string) $e->enrollment_id === $idOnly) {
                return true;
            }

            $name = mb_strtolower((string) ($e->student?->full_name ?? ''));
            if ($name !== '' && str_contains($name, $needle)) {
                return true;
            }

            if (strlen($digits) >= 4) {
                $phones = $e->student?->phones ?? collect();
                foreach ($phones as $phone) {
                    $p = preg_replace('/\D+/', '', (string) ($phone->phone_number ?? ''));
                    if ($p !== '' && str_contains($p, $digits)) {
                        return true;
                    }
                }
            }

            return false;
        })->values();
    }
}
