<?php

namespace Leantime\Plugins\Daily\Hxcontrollers;

use Illuminate\Support\Facades\Log;
use Leantime\Core\Controller\HtmxController;
use Leantime\Plugins\Daily\Models\Habit;
use Leantime\Plugins\Daily\Services\Habits as HabitsService;

class HabittypeDetails extends HtmxController
{
    protected static string $view = 'daily::partials.habittypeSelector';

    private HabitsService $habitsService;

    public function init(
        HabitsService $habitsService
    ) {
        $this->habitsService = $habitsService;
    }

    public function post($params)
    {
        $habit = app()->make(Habit::class,  [
            'values' => $params,
        ]);
        $this->tpl->assign('selectedHabitType', $this->habitsService->getHabitTypeById($habit->habitType));
        $this->tpl->assign('habit', $habit);
    }
}
