<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin
                            {--name= : Ad soyad}
                            {--email= : E-posta}
                            {--password= : Parola}';

    protected $description = 'İçerik paneline giriş yapacak bir yönetici oluşturur';

    public function handle(): int
    {
        $data = [
            'name' => $this->option('name') ?? $this->ask('Ad soyad'),
            'email' => $this->option('email') ?? $this->ask('E-posta'),
            'password' => $this->option('password') ?? $this->secret('Parola'),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:12'],
        ], [
            'password.min' => 'Parola en az 12 karakter olmalı.',
            'email.unique' => 'Bu e-posta ile bir yönetici zaten var.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $admin = Admin::create($data);

        $this->info("Yönetici oluşturuldu: {$admin->email}");
        $this->line('Panel adresi: '.url('/admin'));

        return self::SUCCESS;
    }
}
