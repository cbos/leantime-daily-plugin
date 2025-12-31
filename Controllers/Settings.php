<?php

namespace Leantime\Plugins\Daily\Controllers;

use Leantime\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class Settings extends Controller
{
    public function init(): void
    {
    }

    public function get(): Response
    {

        return $this->tpl->display("daily.settings");
    }

    public function post(array $params): void
    {
    }
}
