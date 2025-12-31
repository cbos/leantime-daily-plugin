<?php

namespace Leantime\Plugins\Daily\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Leantime\Plugins\Daily\Configuration\AppSettings as AppSettingDailyPlugin;
use Leantime\Core\Controller\Controller;
use Leantime\Core\Controller\Frontcontroller;
use Leantime\Plugins\Daily\Repositories\DailyDBUpdateRepository as InstallRepository;
use Leantime\Domain\Setting\Repositories\Setting as SettingRepository;
use Symfony\Component\HttpFoundation\Response;


class Update extends Controller
{
    private InstallRepository $installRepo;

    private SettingRepository $settingsRepo;

    private AppSettingDailyPlugin $appSettings;

    /**
     * init - initialize private variables
     */
    public function init(
        InstallRepository $installRepo,
        SettingRepository $settingsRepo,
        AppSettingDailyPlugin $appSettings
    ) {
        $this->installRepo = $installRepo;
        $this->settingsRepo = $settingsRepo;
        $this->appSettings = $appSettings;
    }

    /**
     * get - handle get requests
     *
     * @params parameters or body of the request
     */
    public function get($params)
    {
        $dbVersion = $this->settingsRepo->getSetting(InstallRepository::$db_version_identifier);
        if ($this->appSettings->dbVersion == $dbVersion) {
            return Frontcontroller::redirect(BASE_URL);
        }

        return $this->tpl->display('daily.updateDB', 'entry');
    }

    /**
     * @throws BindingResolutionException
     */
    public function post($params): Response
    {
        if (isset($_POST['updateDB'])) {
            session()->forget(InstallRepository::$db_version_identifier);
            $success = $this->installRepo->updateDB();

            if (is_array($success) === true) {
                foreach ($success as $errorMessage) {
                    $this->tpl->setNotification('There was a problem.', 'error');
                    // report($errorMessage);
                }
                $this->tpl->setNotification('There was a problem updating your database. Please check your error logs to verify your database is up to date.', 'error');

                return Frontcontroller::redirect(BASE_URL.'/daily/update');
            }

            if ($success === true) {
                return Frontcontroller::redirect(BASE_URL);
            }
        }

        $this->tpl->setNotification('There was a problem.', 'error');

        return Frontcontroller::redirect(BASE_URL.'/daily/update');
    }
}
