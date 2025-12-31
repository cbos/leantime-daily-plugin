<?php

namespace Leantime\Plugins\Daily\Services;

use Leantime\Plugins\Daily\Models\HabitType;
use Leantime\Plugins\Daily\Models\Habit;

class Habits
{
    public function __construct()
    {}

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

    public function getMyHabits()
    {
        $yesNo = app()->make(Habit::class, [
            'values' => [
                'id' => 1,
                'name' => 'CheckIn',
                'habitType' => '0'
            ],
        ]);

        $mood = app()->make(Habit::class, [
            'values' => [
                'id' => 2,
                'name' => 'Mood',
                'habitType' => '1',
                'minValue' => 1,
                'maxValue' => 10
            ],
        ]);

        $location = app()->make(Habit::class, [
            'values' => [
                'id' => 3,
                'name' => 'Location',
                'habitType' => '2',
                'enumValues' => 'ING,OpenValue,Thuis,Conferentie'
            ],
        ]);
        return [$yesNo, $mood, $location];
    }

    public function getHabitById($id){
        $habits = $this->getMyHabits();
        foreach ($habits as $habit) {
            if ($habit->id == $id) {
                return $habit;
            }
        }
        return null;
    }
}
