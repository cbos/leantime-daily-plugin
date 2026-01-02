<?php

namespace Leantime\Plugins\Daily\Models;

class Habit
{
    public mixed $id = null;

    public ?string $name = '';

    public mixed $userId = null;

    public ?int $habitType = null;

    public ?int $numMinValue = null;

    public ?int $numMaxValue = null;

    public ?string $enumValues = null;

    public function __construct(array|bool $values = false)
    {
        if ($values !== false) {
            $this->id = $values['id'] ?? '';
            $this->name = $values['name'] ?? '';
            $this->userId = $values['userId'] ?? '';
            $this->habitType = $values['habitType'] ?? 0;
            $this->numMinValue = $values['numMinValue'] ?? null;
            $this->numMaxValue = $values['numMaxValue'] ?? null;
            $this->enumValues = $values['enumValues'] ?? null;
        }
    }
}