<?php

namespace Leantime\Plugins\Daily\Services;

use Leantime\Plugins\Daily\Models\HabitRecord;
use Leantime\Plugins\Daily\Models\HabitType;
use Leantime\Plugins\Daily\Models\Habit;

use Leantime\Plugins\Daily\Repositories\HabitRepository;

class Habits
{
    public function __construct(private HabitRepository $habitRepository)
    {
    }

    public function getHabitTypes()
    {
        $yesNoHabit = app()->make(HabitType::class, [
            'values' => [
                'id' => 0,
                'name' => 'Yes/no',
                'template' => 'daily-habittypeYesno',
            ],
        ]);

        $numericHabit = app()->make(HabitType::class, [
            'values' => [
                'id' => 1,
                'name' => 'Numeric',
                'template' => 'daily-habittypeNumeric',
            ],
        ]);

        $enumHabit = app()->make(HabitType::class, [
            'values' => [
                'id' => 2,
                'name' => 'Enum/list',
                'template' => 'daily-habittypeEnum',
            ],
        ]);

        return [$yesNoHabit, $numericHabit, $enumHabit];
    }

    public function getHabitTypeById($id)
    {
        $habitTypes = $this->getHabitTypes();
        foreach ($habitTypes as $habitType) {
            if ($habitType->id == $id) {
                return $habitType;
            }
        }
        return null;
    }

    public function getMyHabits(): array
    {
        return $this->habitRepository->getHabitsByCurrentUser();
    }

    public function getHabitById($id)
    {
        $habits = $this->getMyHabits();
        foreach ($habits as $habit) {
            if ($habit->id == $id) {
                return $habit;
            }
        }
        return null;
    }

    public function addHabit(Habit $habit): string|bool
    {
        return $this->habitRepository->addHabit($habit);
    }

    public function editHabit(Habit $habit): void
    {
        $this->habitRepository->editHabit($habit);
    }

    public function deleteHabit(int $id): int|false
    {
        return $this->habitRepository->deleteHabit($id);
    }

    public function addHabitRecord(HabitRecord $habitRecord): string|bool
    {
        return $this->habitRepository->addHabitRecord($habitRecord);
    }

    public function editHabitRecord(HabitRecord $habitRecord): void
    {
        $this->habitRepository->editHabitRecord($habitRecord);
    }

    public function getMyHabitRecordsFor(string $date): array
    {
        return $this->habitRepository->getHabitRecordsByCurrentUserByDate($date);
    }
}
