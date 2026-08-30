<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendWeeklyMilestone;
use App\Models\Pregnancy;
use Illuminate\Console\Command;

class DispatchWeeklyMilestones extends Command
{
    protected $signature = 'app:dispatch-weekly-milestones';

    protected $description = 'Yeni haftaya giren gebelikler için haftalık bildirimi kuyruğa alır';

    public function handle(): int
    {
        $count = 0;

        Pregnancy::query()
            ->active()
            ->with('user')
            ->chunkById(200, function ($pregnancies) use (&$count): void {
                foreach ($pregnancies as $pregnancy) {
                    // Kullanıcının kendi takvim gününe göre tam hafta dönüşü mü?
                    // Sunucu gününe bakmak, saat dilimine göre bildirimi bir gün
                    // kaydırır veya büsbütün atlar.
                    if ($pregnancy->gestationalAge()['day'] !== 0) {
                        continue;
                    }

                    SendWeeklyMilestone::dispatch($pregnancy->id);
                    $count++;
                }
            });

        $this->info("{$count} haftalık bildirim kuyruğa alındı.");

        return self::SUCCESS;
    }
}
