<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TestTelegramBot extends Command
{
    protected $signature = 'telegram:test';
    protected $description = 'Telegram bot bağlantısını test et';

    public function handle()
    {
        $this->info('Telegram bot test ediliyor...');
        
        $telegramService = new TelegramService();
        $result = $telegramService->sendTestMessage();

        if ($result) {
            $this->info('✅ Test mesajı başarıyla gönderildi!');
            $this->info('📱 Telegram botunuzu kontrol edin.');
            return 0;
        } else {
            $this->error('❌ Test mesajı gönderilemedi.');
            $this->error('Lütfen .env dosyasındaki TELEGRAM_BOT_TOKEN ve TELEGRAM_CHAT_ID değerlerini kontrol edin.');
            return 1;
        }
    }
}

