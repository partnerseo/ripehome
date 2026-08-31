<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * KVKK gereği: kullanıcı verisini dışa aktarma ve kalıcı silme.
 *
 * İkisi de destek talebi gerektirmeden, uygulama içinden yapılabilmeli.
 */
class AccountDataService
{
    /**
     * Kullanıcının tüm verisi, okunabilir bir yapıda.
     *
     * @return array<string, mixed>
     */
    public function export(User $user): array
    {
        $user->load([
            'consents',
            'devices',
            'pregnancies.redatings',
            'pregnancies.appointments',
            'pregnancies.healthLogs',
            'pregnancies.kickSessions.events',
            'pregnancies.contractionSessions.contractions',
            'pregnancies.symptomLogs',
        ]);

        return [
            'exported_at' => now()->toIso8601String(),
            'user' => [
                'email' => $user->email,
                'name' => $user->name,
                'locale' => $user->locale,
                'timezone' => $user->timezone,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
            'consents' => $user->consents->map(fn ($c): array => [
                'version' => $c->version,
                'accepted_at' => $c->accepted_at?->toIso8601String(),
                'withdrawn_at' => $c->withdrawn_at?->toIso8601String(),
            ])->all(),
            'devices' => $user->devices->map(fn ($d): array => [
                'platform' => $d->platform,
                'timezone' => $d->timezone,
                'last_seen_at' => $d->last_seen_at?->toIso8601String(),
            ])->all(),
            'pregnancies' => $user->pregnancies->map(fn (Pregnancy $p): array => [
                'method' => $p->method,
                'input_date' => $p->input_date->toDateString(),
                'cycle_length' => $p->cycle_length,
                'lmp_date' => $p->lmp_date->toDateString(),
                'due_date' => $p->due_date->toDateString(),
                'baby_count' => $p->baby_count,
                'status' => $p->status,
                'ended_at' => $p->ended_at?->toIso8601String(),
                'ended_reason' => $p->ended_reason,
                'redatings' => $p->redatings->map(fn ($r): array => [
                    'measured_on' => $r->measured_on->toDateString(),
                    'ga_days_at_measure' => $r->ga_days_at_measure,
                    'source' => $r->source,
                ])->all(),
                'appointments' => $p->appointments->map(fn ($a): array => [
                    'title' => $a->title,
                    'category' => $a->category,
                    'scheduled_at' => $a->scheduled_at?->toIso8601String(),
                    'completed_at' => $a->completed_at?->toIso8601String(),
                    'notes' => $a->notes,
                ])->all(),
                'health_logs' => $p->healthLogs->map(fn ($l): array => [
                    'type' => $l->type,
                    'value_1' => $l->value_1,
                    'value_2' => $l->value_2,
                    'unit' => $l->unit,
                    'measured_on' => $l->measured_on->toDateString(),
                    'note' => $l->note,
                ])->all(),
                'kick_sessions' => $p->kickSessions->map(fn ($s): array => [
                    'started_at' => $s->started_at->toIso8601String(),
                    'ended_at' => $s->ended_at?->toIso8601String(),
                    'kick_count' => $s->kick_count,
                    'events' => $s->events->map(fn ($e): string => $e->occurred_at->toIso8601String())->all(),
                ])->all(),
                'contraction_sessions' => $p->contractionSessions->map(fn ($s): array => [
                    'started_at' => $s->started_at->toIso8601String(),
                    'contractions' => $s->contractions->map(fn ($c): array => [
                        'started_at' => $c->started_at->toIso8601String(),
                        'duration_sec' => $c->duration_sec,
                        'interval_sec' => $c->interval_sec,
                    ])->all(),
                ])->all(),
                'symptom_logs' => $p->symptomLogs->map(fn ($l): array => [
                    'logged_on' => $l->logged_on->toDateString(),
                    'symptoms' => $l->symptoms ?? [],
                    'mood' => $l->mood,
                    'note' => $l->note,
                ])->all(),
            ])->all(),
        ];
    }

    /**
     * Hesabı ve tüm verisini kalıcı olarak siler.
     *
     * Cihaz kayıtları da gider: silinen hesaba bildirim gönderilecek bir yol
     * kalmamalı. Bağlı tablolar yabancı anahtar zinciriyle temizlenir.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->tokens()->delete();
            $user->devices()->delete();
            $user->delete();
        });
    }
}
