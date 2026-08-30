<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Services\Push\PushMessage;
use App\Services\Push\PushSender;

/** Gönderilen bildirimleri toplayan test yerine geçeni. */
class FakePushSender implements PushSender
{
    /** @var list<array{tokens: list<string>, message: PushMessage}> */
    public array $sent = [];

    public function send(array $tokens, PushMessage $message): void
    {
        $this->sent[] = ['tokens' => $tokens, 'message' => $message];
    }

    public function count(): int
    {
        return count($this->sent);
    }
}
