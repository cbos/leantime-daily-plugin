<?php

namespace Leantime\Plugins\Daily\Repositories;

use Leantime\Core\Db\Db as DbCore;
use Leantime\Core\Db\Repository as RepositoryCore;
use Leantime\Plugins\Daily\Models\Habit;
use Leantime\Plugins\Daily\Models\HabitRecord;

use PDO;

class HabitRepository extends RepositoryCore
{

    /**
     * Class constructor.
     *
     * @param  DbCore  $db  The DbCore object.
     * @return void
     */
    public function __construct(
        private DbCore $db,
    ) {}

    public function addHabit(Habit $habit): false|string
    {
        $query = 'INSERT INTO zp_habit (userId, name, habitType, numMinValue, numMaxValue, enumValues) 
                    VALUES (:userId, :name, :habitType, :numMinValue, :numMaxValue, :enumValues)';

        $stmn = $this->db->database->prepare($query);
        $stmn->bindValue(':userId', session('userdata.id'), PDO::PARAM_INT);
        $stmn->bindValue(':name', $habit->name, PDO::PARAM_STR);
        $stmn->bindValue(':habitType', $habit->habitType, PDO::PARAM_INT);
        $stmn->bindValue(':numMinValue', $habit->numMinValue, PDO::PARAM_INT);
        $stmn->bindValue(':numMaxValue', $habit->numMaxValue, PDO::PARAM_INT);
        $stmn->bindValue(':enumValues', $habit->enumValues, PDO::PARAM_STR);

        if ($stmn->execute()) {
            $id = $this->db->database->lastInsertId();
            $stmn->closeCursor();

            return $id;
        } else {
            $stmn->closeCursor();

            return false;
        }
    }

    public function editHabit(Habit $habit): void
    {
        $query = 'UPDATE zp_habit SET 
                    name = :name,
                    numMinValue = :numMinValue, 
                    numMaxValue = :numMaxValue, 
                    enumValues = :enumValues 
                WHERE id = :id AND userId = :userId LIMIT 1';


        $stmn = $this->db->database->prepare($query);

        $stmn->bindValue(':userId', session('userdata.id'), PDO::PARAM_INT);
        $stmn->bindValue(':id', $habit->id, PDO::PARAM_INT);
        $stmn->bindValue(':name', $habit->name, PDO::PARAM_STR);
        $stmn->bindValue(':numMinValue', $habit->numMinValue, PDO::PARAM_INT);
        $stmn->bindValue(':numMaxValue', $habit->numMaxValue, PDO::PARAM_INT);
        $stmn->bindValue(':enumValues', $habit->enumValues, PDO::PARAM_STR);

        $stmn->execute();
        $stmn->closeCursor();
    }

    public function deleteHabit(int $id): int|false
    {
        $query = 'DELETE FROM zp_habit WHERE id = :id AND userId = :userId LIMIT 1';

        $stmn = $this->db->database->prepare($query);
        $stmn->bindValue(':id', $id, PDO::PARAM_INT);
        $stmn->bindValue(':userId', session('userdata.id'), PDO::PARAM_INT);
        $value = $stmn->execute();
        $stmn->closeCursor();

        return $value;
    }

    public function getHabitsByCurrentUser(): array
    {
        $query = 'SELECT * FROM zp_habit WHERE userId = :userId';

        $stmn = $this->db->database->prepare($query);
        $stmn->bindValue(':userId', session('userdata.id'), PDO::PARAM_INT);

        $stmn->execute();
        $values = $stmn->fetchAll(PDO::FETCH_CLASS, '\Leantime\Plugins\Daily\Models\Habit');
        $stmn->closeCursor();

        return $values;
    }

    public function addHabitRecord(HabitRecord $habitRecord): false|string
    {
        $query = 'INSERT INTO zp_habitrecord (userId, habitId, date, value)  
                    VALUES (:userId, :habitId, :date, :value)';

        $stmn = $this->db->database->prepare($query);
        $stmn->bindValue(':userId', session('userdata.id'), PDO::PARAM_INT);
        $stmn->bindValue(':habitId', $habitRecord->habitId, PDO::PARAM_INT);
        $stmn->bindValue(':date', $habitRecord->date, PDO::PARAM_STR);
        $stmn->bindValue(':value', $habitRecord->value, PDO::PARAM_STR);

        if ($stmn->execute()) {
            $id = $this->db->database->lastInsertId();
            $stmn->closeCursor();

            return $id;
        } else {
            $stmn->closeCursor();

            return false;
        }
    }

    public function editHabitRecord(HabitRecord $habitRecord): void
    {
        $query = 'UPDATE zp_habitrecord SET 
                    value = :value 
                WHERE id = :id AND userId = :userId AND date = :date LIMIT 1';

        $stmn = $this->db->database->prepare($query);

        $stmn->bindValue(':id', $habitRecord->id, PDO::PARAM_INT);
        $stmn->bindValue(':userId', session('userdata.id'), PDO::PARAM_INT);
        $stmn->bindValue(':date', $habitRecord->date, PDO::PARAM_STR);
        $stmn->bindValue(':value', $habitRecord->value, PDO::PARAM_STR);

        $stmn->execute();
        $stmn->closeCursor();
    }

    public function getHabitRecordsByCurrentUserByDate(string $date): array
    {
        $query = 'SELECT * FROM zp_habitrecord WHERE userId = :userId AND date = :date';

        $stmn = $this->db->database->prepare($query);
        $stmn->bindValue(':userId', session('userdata.id'), PDO::PARAM_INT);
        $stmn->bindValue(':date', $date, PDO::PARAM_STR);

        $stmn->execute();
        $values = $stmn->fetchAll(PDO::FETCH_CLASS, '\Leantime\Plugins\Daily\Models\HabitRecord');
        $stmn->closeCursor();

        return $values;
    }

    public function getHabitRecordsByCurrentUserByYear(string $year): array
    {
        $query = 'SELECT * FROM zp_habitrecord WHERE userId = :userId AND date >= :yearStart AND date <= :yearEnd';

        $stmn = $this->db->database->prepare($query);
        $stmn->bindValue(':userId', session('userdata.id'), PDO::PARAM_INT);
        $stmn->bindValue(':yearStart', $year."-01-01", PDO::PARAM_STR);
        $stmn->bindValue(':yearEnd', $year."-12-31", PDO::PARAM_STR);

        $stmn->execute();
        $values = $stmn->fetchAll(PDO::FETCH_CLASS, '\Leantime\Plugins\Daily\Models\HabitRecord');
        $stmn->closeCursor();

        return $values;
    }
}

