<?php

namespace App\Dto;

class PageableDto
{
    public function __construct(
        public int $page,
        public int $size
    ) {}

    public static function of(int $page, int $size): self
    {
        return new self($page, $size);
    }

    public function getPageNumber(): int
    {
        return max(1, $this->page);
    }

    public function getPageSize(): int
    {
        return $this->size > 0 ? $this->size : 10;
    }
}
