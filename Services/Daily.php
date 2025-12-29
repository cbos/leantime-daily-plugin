<?php

namespace Leantime\Plugins\Daily\Services;

use Leantime\Plugins\Daily\Repositories\DailyRepository as DailyRepository;

class Daily
{
    public function __construct(
        private DailyRepository $dailyRepository
    ) {}

    public function install(): void
    {
        // Repo call to create tables.
    }

    public function uninstall(): void
    {
        // Remove tables
    }
}
