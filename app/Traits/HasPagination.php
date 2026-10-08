<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;

trait HasPagination
{
    public static function paginate($resource): ResourceCollection
    {
        if (! ($resource instanceof AbstractPaginator)) {
            return self::collection($resource);
        }

        return new class($resource, self::class) extends ResourceCollection
        {

            public function __construct($resource, string $collects)
            {
                $this->collects = $collects;
                parent::__construct($resource);
            }

            public function toArray($request): array
            {
                return [
                    'current_page' => $this->currentPage(),
                    'data' => $this->collection,
                    'first_page_url' => $this->url(1),
                    'from' => $this->firstItem(),
                    'last_page' => $this->lastPage(),
                    'last_page_url' => $this->url($this->lastPage()),
                    'links' => $this->linkCollection()->toArray(),
                    'next_page_url' => $this->nextPageUrl(),
                    'path' => $this->path(),
                    'per_page' => $this->perPage(),
                    'prev_page_url' => $this->previousPageUrl(),
                    'to' => $this->lastItem(),
                    'total' => $this->total(),
                ];
            }
        };
    }

    public static function cursorPaginate($resource): ResourceCollection
    {
        if (! ($resource instanceof AbstractCursorPaginator)) {
            return self::collection($resource);
        }

        return new class($resource, self::class) extends ResourceCollection
        {
            public function __construct($resource, string $collects)
            {
                $this->collects = $collects;
                parent::__construct($resource);
            }

            public function toArray($request): array
            {
                return [

                    'data' => $this->collection,
                    'per_page' => $this->perPage(),
                    'next_cursor' => $this->nextCursor()?->encode(),
                    'prev_cursor' => $this->previousCursor()?->encode(),
                    'next_page_url' => $this->nextPageUrl(),
                    'prev_page_url' => $this->previousPageUrl(),
                    'path' => $this->path(),
                ];
            }
        };
    }
}
