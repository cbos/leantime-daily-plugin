<?php

namespace Leantime\Plugins\Daily\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Users\Services\Users as UserService;
use Leantime\Plugins\Daily\Services\Habits as HabitsService;
use Symfony\Component\HttpFoundation\Response;

class Habits extends Controller
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
        $this->tpl->assign('habits', $this->habitsService->getMyHabits());
        return $this->tpl->display("daily.habits");
    }
}
