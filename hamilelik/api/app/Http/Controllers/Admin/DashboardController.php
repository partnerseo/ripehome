<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreeningTemplate;
use App\Models\WeekContent;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Panelin açılışı tek soruyu cevaplar: neyin eksik olduğu. */
    public function __invoke(): View
    {
        $weeks = WeekContent::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $screenings = ScreeningTemplate::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.dashboard', [
            'weekPublished' => (int) ($weeks[WeekContent::STATUS_PUBLISHED] ?? 0),
            'weekPending' => (int) $weeks->sum() - (int) ($weeks[WeekContent::STATUS_PUBLISHED] ?? 0),
            'weekMissing' => 42 - (int) $weeks->sum(),
            'screeningPublished' => (int) ($screenings[ScreeningTemplate::STATUS_PUBLISHED] ?? 0),
            'screeningPending' => (int) $screenings->sum() - (int) ($screenings[ScreeningTemplate::STATUS_PUBLISHED] ?? 0),
            'reviewQueue' => WeekContent::query()->where('status', WeekContent::STATUS_IN_REVIEW)->count()
                + ScreeningTemplate::query()->where('status', ScreeningTemplate::STATUS_IN_REVIEW)->count(),
        ]);
    }
}
