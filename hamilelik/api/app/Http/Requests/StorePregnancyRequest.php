<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Closure;
use Hamilelik\Engine\GestationalAge;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class StorePregnancyRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'method' => ['required', Rule::in(['lmp', 'due_date', 'conception', 'ivf_d5', 'ivf_d3'])],
            'input_date' => ['required', 'date_format:Y-m-d', $this->notInTheFuture()],
            'cycle_length' => ['sometimes', 'integer', 'between:20,45'],
            'baby_count' => ['sometimes', 'integer', 'between:1,4'],
        ];
    }

    /**
     * Girdinin gelecekte bir gebelik başlangıcı vermediğini doğrular.
     *
     * Bu kontrol burada yapılmazsa kayıt oluşur ve hata ancak yanıt
     * serileştirilirken patlar: kullanıcı 422 yerine 500 görür, ortada da
     * hesaplanamayan bir gebelik kaydı kalır.
     */
    private function notInTheFuture(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $method = $this->input('method');

            if (! is_string($method) || ! is_string($value)) {
                return; // Biçim hatasını diğer kurallar bildirir.
            }

            try {
                $lmp = GestationalAge::effectiveLmp($method, $value, (int) $this->input('cycle_length', 28));
            } catch (InvalidArgumentException) {
                return; // Yöntem/biçim hatasını diğer kurallar bildirir.
            }

            $today = Carbon::now($this->user()?->timezone ?? config('app.timezone'))->toDateString();

            if ($lmp > $today) {
                $fail('Girilen tarih gelecekte bir gebelik başlangıcı veriyor.');
            }
        };
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'method.in' => 'Geçersiz giriş yöntemi.',
            'input_date.date_format' => 'Tarih YYYY-AA-GG biçiminde olmalı.',
            'cycle_length.between' => 'Döngü uzunluğu 20-45 gün aralığında olmalı.',
        ];
    }
}
