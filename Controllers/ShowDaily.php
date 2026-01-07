<?php

namespace Leantime\Plugins\Daily\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Leantime\Core\Controller\Controller;
use Leantime\Core\Controller\Frontcontroller;
use Leantime\Core\Support\FromFormat;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Users\Services\Users as UserService;
use Leantime\Plugins\Daily\Services\Habits as HabitsService;
use Symfony\Component\HttpFoundation\Response;

class ShowDaily extends Controller
{
    private HabitsService $habitsService;

    public function init(
        HabitsService $habitsService
    ): void {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

        $this->habitsService = $habitsService;
    }

    public function get($params): Response
    {
        if (! isset($params['selectedDate'])) {
            return $this->tpl->displayPartial('daily.error400', responseCode: 400);
        }

        $selectedDate = $params['selectedDate'];
        $this->tpl->assign('habits', $this->habitsService->getMyHabits());
        $this->tpl->assign('habitRecords', $this->habitsService->getMyHabitRecordsFor($selectedDate));
        $this->tpl->assign('selectedDate', $selectedDate);

        return $this->tpl->displayPartial('daily.showDailyModal');
    }
}