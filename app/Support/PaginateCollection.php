<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class PaginateCollection
{
    public static function make(Collection $items, int $perPage = 25, string $pageName = 'page', array $exceptQuery = []): LengthAwarePaginator
    {
        $page  = max(1, (int) Paginator::resolveCurrentPage($pageName));
        $items = $items->values();

        $lastPage = max(1, (int) ceil($items->count() / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path'     => Paginator::resolveCurrentPath(),
                'query'    => collect(request()->query())->except($exceptQuery)->all(),
                'pageName' => $pageName,
            ]
        );
    }
}
