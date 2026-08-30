<?php

declare(strict_types=1);

namespace App\Services\Push;

interface PushSender
{
    /**
     * @param  list<string>  $tokens  Expo push jetonları.
     */
    public function send(array $tokens, PushMessage $message): void;
}
