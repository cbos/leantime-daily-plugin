<?php

namespace Leantime\Plugins\Daily\Hxcontrollers;

use Illuminate\Support\Facades\Log;
use Leantime\Core\Controller\HtmxController;
use Leantime\Plugins\Daily\Models\Habit;
use Leantime\Plugins\Daily\Models\HabitRecord;
use Leantime\Plugins\Daily\Services\Habits as HabitsService;

class RecordHabit extends HtmxController
{
    protected static string $view = 'daily::partials.habit';

    private HabitsService $habitsService;

    public function init(
        HabitsService $habitsService
    ) {
        $this->habitsService = $habitsService;
    }

    public function post($params)
    {
        Log::error(var_export($params, true));
        $habit = $this->habitsService->getHabitById($params['habitId']);

        $habitRecord = app()->make(HabitRecord::class,  [
            'values' => false,
        ]);

        $habitRecord->id = $params['habitRecordId'];
        $habitRecord->habitId = $habit->id;
        $habitRecord->date = $params['selectedDate'];
        $habitRecord->value = $params['habitValue'];

        $this->tpl->assign('habit', $habit);
        $this->tpl->assign('habitRecord', $habitRecord);
        $this->tpl->assign('selectedDate', $params['selectedDate']);
    }
}
