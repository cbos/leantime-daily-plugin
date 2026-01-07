<?php

namespace Leantime\Plugins\Daily\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Leantime\Core\Controller\Controller;
use Leantime\Core\Controller\Frontcontroller;
use Leantime\Core\Support\FromFormat;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Users\Services\Users as UserService;
use Leantime\Plugins\Daily\Models\Habit;
use Leantime\Plugins\Daily\Services\Habits as HabitsService;
use Symfony\Component\HttpFoundation\Response;

class ShowHabit extends Controller
{
    private UserService $userService;

    private HabitsService $habitsService;

    public function init(
        UserService $userService,
        HabitsService $habitsService
    ): void {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);

        $this->userService = $userService;
        $this->habitsService = $habitsService;
    }

    public function get($params): Response
    {
        if (! isset($params['id'])) {
            return $this->tpl->displayPartial('daily.error400', responseCode: 400);
        }

        $id = (int) ($params['id']);

        $habit = $this->habitsService->getHabitById($id);

        $this->tpl->assign('habit', $habit);
        $this->tpl->assign('selectedHabitType', $this->habitsService->getHabitTypeById($habit->habitType));

        return $this->tpl->displayPartial('daily.showHabitModal');
    }

    public function post($params): Response
    {
        if (isset($params['saveTicket']) || isset($params['saveAndCloseTicket'])) {

            $habit = app()->make(Habit::class,  [
                'values' => $params,
            ]);

            $this->habitsService->editHabit($habit);

            if (isset($params['saveAndCloseTicket']) === true && $params['saveAndCloseTicket'] == 1) {
                return Frontcontroller::redirect(BASE_URL.'/daily/showHabit/'.$habit->id.'?closeModal=1');
            } else {
                $habit = $this->habitsService->getHabitById($habit->id);
                $this->tpl->assign('habit', $habit);
                $this->tpl->assign('selectedHabitType', $this->habitsService->getHabitTypeById($habit->habitType));

                return $this->tpl->displayPartial('daily.showHabitModal');
            }
        }
        return Frontcontroller::redirect(BASE_URL.'/daily/habits');
    }
}