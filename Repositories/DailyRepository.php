<?php

namespace Leantime\Plugins\Daily\Repositories;

use Leantime\Core\Db\Db as DbCore;

class DailyRepository
{
    private DbCore $db;

    /**
     * __construct - get database connection
     */
    public function __construct(DbCore $db)
    {
        $this->db = $db;
    }

    // Repo methods here.
}

