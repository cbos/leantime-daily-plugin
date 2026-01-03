<?php

namespace Leantime\Plugins\Daily\Hxcontrollers;

use Illuminate\Support\Facades\Log;
use Leantime\Core\Controller\HtmxController;
use Leantime\Plugins\Daily\Models\Habit;
use Leantime\Plugins\Daily\Services\Habits as HabitsService;

class MyDaily extends HtmxController
{
    protected static string $view = 'daily::partials.myDaily';

    private HabitsService $habitsService;

    public function init(
        HabitsService $habitsService
    ) {
        $this->habitsService = $habitsService;
    }

    public function get()
    {
        $this->tpl->assign('habits', $this->habitsService->getMyHabits());
        $this->tpl->assign('selectedDate', date('Y-m-d'));
    }
}
