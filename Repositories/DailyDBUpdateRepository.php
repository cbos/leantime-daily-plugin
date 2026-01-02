<?php

namespace Leantime\Plugins\Daily\Repositories;

use Leantime\Core\Db\Db as DbCore;
use Leantime\Domain\Setting\Repositories\Setting;
use Leantime\Domain\Setting\Services\SettingCache;
use Leantime\Plugins\Daily\Configuration\AppSettings as AppSettingDailyPlugin;
use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;

class DailyDBUpdateRepository
{
    private DbCore $db;

    private AppSettingDailyPlugin $appSettings;

    public static String $db_version_identifier = 'daily-db-version';

    /**
     * db update scripts listed out by version number with leading zeros A.BB.CC => ABBCC
     */
    private array $dbUpdates = [
        10000,
    ];

    /**
     * __construct - get database connection
     */
    public function __construct(DbCore $db, AppSettingDailyPlugin $appSettings)
    {
        $this->db = $db;
        $this->appSettings = $appSettings;
    }

    public function updateDB(): array|bool
    {

        // Forget all the versions we think we know and start fresh
        session()->forget(self::$db_version_identifier);
        $settingsCacheService = app()->make(SettingCache::class);
        $settingsCacheService->forget(self::$db_version_identifier);

        $errors = [];

        $versionArray = explode('.', $this->appSettings->dbVersion);
        if (is_array($versionArray) && count($versionArray) == 3) {
            $major = $versionArray[0];
            $minor = str_pad($versionArray[1], 2, '0', STR_PAD_LEFT);
            $patch = str_pad($versionArray[2], 2, '0', STR_PAD_LEFT);
            $newDBVersion = $major.$minor.$patch;
        } else {
            $errors[0] = 'Problem identifying the version number';

            return $errors;
        }

        $setting = app()->make(Setting::class);
        $dbVersion = $setting->getSetting(self::$db_version_identifier);
        $currentDBVersion = 0;
        if ($dbVersion) {
            $versionArray = explode('.', $dbVersion);
            if (is_array($versionArray) && count($versionArray) == 3) {
                $major = $versionArray[0];
                $minor = str_pad($versionArray[1], 2, '0', STR_PAD_LEFT);
                $patch = str_pad($versionArray[2], 2, '0', STR_PAD_LEFT);
                $currentDBVersion = $major.$minor.$patch;
            } else {
                $errors[0] = 'Problem identifying the version number';

                return $errors;
            }
        }

        Log::error('Current DB Version: '.$currentDBVersion);
        Log::error('New DB Version: '.$newDBVersion);

        if ($currentDBVersion == $newDBVersion) {

            session()->forget('isUpdated');
            session()->forget('dbVersion');

            return true;
        }

        // Find all update functions that need to be executed
        foreach ($this->dbUpdates as $updateVersion) {
            if ($currentDBVersion < $updateVersion) {
                Log::info('Updating DB to version '.$updateVersion);
                $functionName = 'update_sql_'.$updateVersion;

                $result = $this->$functionName();

                if ($result !== true) {
                    $errors = array_merge($errors, $result);
                } else {
                    // Update version number in db
                    try {

                        $settingsService = app()->make(Setting::class);
                        $settingsService->saveSetting(self::$db_version_identifier, $this->convert_version($updateVersion));

                        $currentDBVersion = $updateVersion;
                    } catch (PDOException $e) {
                        Log::error($e);
                        Log::error($e->getTraceAsString());

                        return ['There was a problem updating the database'];
                    }
                }

                if (count($errors) > 0) {
                    return $errors;
                }
            }
        }

        session()->forget('isUpdated');
        session()->forget('dbVersion');

        return true;
    }

    private function convert_version($inputVersion): string
    {
        // $inputVersion is in the format of 10000 and needs to be converted to 1.0.0
        $versionString = str_pad((string) $inputVersion, 5, '0', STR_PAD_LEFT);

        $major = intval(substr($versionString, 0, -4));
        $minor = intval(substr($versionString, -4, 2));
        $patch = intval(substr($versionString, -2)  );

        return $major . '.' . $minor . '.' . $patch;
    }

    /**
     * update_sql_10000 - create habits table
     *
     * @noinspection SqlResolve - A lot of tables don't exist anymore, so this will not resolve. Keeping the update script for backwards compatibility
     */
    private function update_sql_10000(): bool|array
    {

        $errors = [];

        $sql = [
            'CREATE TABLE `zp_habit` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `userId` int(11) DEFAULT NULL,
                    `name` varchar(200) DEFAULT NULL,
                    `habitType` TINYINT DEFAULT NULL,
                    `numMinValue` SMALLINT DEFAULT NULL,
                    `numMaxValue` SMALLINT DEFAULT NULL,
                    `enumValues` varchar(400) DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_habit_userId` (`userId`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        ];

        foreach ($sql as $statement) {
            try {
                $stmn = $this->db->database->prepare($statement);
                $stmn->execute();
            } catch (PDOException $e) {
                Log::error($statement.' Failed:'.$e->getMessage());
                Log::error($e);
                array_push($errors, $statement.' Failed:'.$e->getMessage());
            }
        }

        if (count($errors) > 0) {
            return $errors;
        } else {
            return true;
        }
    }
}

