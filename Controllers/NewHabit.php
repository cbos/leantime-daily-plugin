<?php

namespace Leantime\Plugins\Daily\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Core\Controller\Frontcontroller;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Users\Services\Users as UserService;
use Leantime\Plugins\Daily\Models\Habit;
use Leantime\Plugins\Daily\Services\Habits as HabitsService;
use Symfony\Component\HttpFoundation\Response;

class NewHabit extends Controller
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

    public function get(): Response
    {
        $habit = app()->make(Habit::class, [
            'values' => [
                'habitType' => '0'
            ],
        ]);

        $this->tpl->assign('habit', $habit);
        $this->tpl->assign('habitTypes', $this->habitsService->getHabitTypes());
        $this->tpl->assign('selectedHabitType', $this->habitsService->getHabitTypeById($habit->habitType));

        return $this->tpl->displayPartial('daily.newHabitModal');
    }

    public function post($params): Response
    {
        if (isset($params['saveTicket']) || isset($params['saveAndCloseTicket'])) {

            $habit = app()->make(Habit::class,  [
                'values' => $params,
            ]);

            $result = $this->habitsService->addHabit($habit);

            if (is_array($result) === false) {
                $this->tpl->setNotification($this->language->__('notifications.ticket_saved'), 'success');

                if (isset($params['saveAndCloseTicket']) === true && $params['saveAndCloseTicket'] == 1) {
                    return Frontcontroller::redirect(BASE_URL.'/daily/showHabit/'.$result.'?closeModal=1');
                } else {
                    return Frontcontroller::redirect(BASE_URL.'/daily/showHabit/'.$result);
                }
            } else {
                $this->tpl->setNotification($this->language->__($result['msg']), 'error');

                $this->tpl->assign('habit', $habit);
                $this->tpl->assign('habitTypes', $this->habitsService->getHabitTypes());
                $this->tpl->assign('selectedHabitType', $this->habitsService->getHabitTypeById($habit->habitType));

                return $this->tpl->displayPartial('daily.newHabitModal');
            }
        }
        return Frontcontroller::redirect(BASE_URL.'/daily/habits');
    }
}