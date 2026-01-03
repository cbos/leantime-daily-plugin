<?php

namespace Leantime\Plugins\Daily\Models;

class HabitRecord
{
    public mixed $id = null;

    public mixed $userId = null;

    public ?int $habitId = null;

    public ?string $value = null;

    public ?string $date = null;

    public function __construct(array|bool $values = false)
    {
        if ($values !== false) {
            $this->id = $values['id'] ?? '';
            $this->userId = $values['userId'] ?? '';
            $this->habitId = $values['habitId'] ?? null;
            $this->value = $values['value'] ?? null;
            $this->date = $values['date'] ?? date('Y-m-d');
        }
    }
}