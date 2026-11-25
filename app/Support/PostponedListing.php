<?php

namespace App\Support;

use App\Models\Enrollment\Postponement;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PostponedListing
{
    public const PER_PAGE = 24;

    public static function data(Request $request): array
    {
        $tab    = in_array($request->query('tab'), ['group', 'private'], true) ? $request->query('tab') : 'group';
        $mode   = in_array($request->query('mode'), ['Online', 'Offline'], true) ? $request->query('mode') : 'all';
        $status = in_array($request->query('status'), ['active', 'expired', 'all'], true) ? $request->query('status') : 'active';
        $search = trim((string) $request->query('q', ''));

        $with = [
            'enrollment.student.phones',
            'enrollment.courseTemplate',
            'enrollment.level',
            'enrollment.sublevel',
            'enrollment.privateBundle',
            'enrollment.levelPackage',
            'enrollment.attendances',
            'enrollment.courseInstance.courseTemplate',
            'createdBy',
        ];

        $statuses = match ($status) {
            'expired' => ['Expired'],
            'all'     => ['Active', 'Expired'],
            default   => ['Active'],
        };

        $base = fn(string $type) => Postponement::with($with)
            ->whereHas('enrollment', function ($q) use ($type, $mode) {
                $q->where('enrollment_type', $type);
                if ($mode !== 'all') {
                    $q->where('delivery_mood', $mode);
                }
            })
            ->whereIn('status', $statuses)
            ->orderBy('status')
            ->orderBy('expected_return_date')
            ->get();

        $group   = self::search($base('Group'), $search);
        $private = self::search($base('Private'), $search);

        $stats = [
            'active'        => Postponement::where('status', 'Active')->whereHas('enrollment')->count(),
            'expired'       => Postponement::where('status', 'Expired')->whereHas('enrollment')->count(),
            'returned'      => Postponement::where('status', 'Returned')->whereHas('enrollment')->count(),
            'expiring_soon' => Postponement::where('status', 'Active')
                                    ->whereHas('enrollment')
                                    ->whereDate('expected_return_date', '<=', now()->addDays(7))
                                    ->count(),
        ];

        return [
            'groupPostponed'   => PaginateCollection::make($group, self::PER_PAGE, 'group_page', ['private_page']),
            'privatePostponed' => PaginateCollection::make($private, self::PER_PAGE, 'private_page', ['group_page']),
            'stats'            => $stats,
            'ppTab'            => $tab,
            'ppMode'           => $mode,
            'ppStatus'         => $status,
            'ppSearch'         => $search,
        ];
    }

    private static function search(Collection $rows, string $query): Collection
    {
        if ($query === '') {
            return $rows->values();
        }

        $needle = mb_strtolower($query);
        $digits = preg_replace('/\D+/', '', $query);
        $idOnly = ltrim($query, '#');

        return $rows->filter(function ($pp) use ($needle, $digits, $idOnly) {
            $e = $pp->enrollment;
            if (!$e) {
                return false;
            }

            if (ctype_digit($idOnly) && (string) $e->enrollment_id === $idOnly) {
                return true;
            }

            $haystacks = [
                mb_strtolower((string) ($e->student?->full_name ?? '')),
                mb_strtolower((string) ($e->courseTemplate?->name ?? $e->courseInstance?->courseTemplate?->name ?? '')),
            ];
            foreach ($haystacks as $h) {
                if ($h !== '' && str_contains($h, $needle)) {
                    return true;
                }
            }

            if (strlen($digits) >= 4) {
                foreach ($e->student?->phones ?? [] as $phone) {
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
