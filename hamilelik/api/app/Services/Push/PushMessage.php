<?php

declare(strict_types=1);

namespace App\Services\Push;

final readonly class PushMessage
{
    /** @param array<string, mixed> $data */
    public function __construct(
        public string $title,
        public string $body,
        public array $data = [],
    ) {}
}
