<?php

namespace Leantime\Plugins\Daily\Models;

class Habit
{
    public mixed $id = null;

    public ?string $name = '';

    public ?string $description = '';

    public mixed $userId = null;

    public ?int $habitType = null;

    public ?int $minValue = null;

    public ?int $maxValue = null;

    public ?string $enumValues = null;

    public function __construct(array|bool $values = false)
    {
        if ($values !== false) {
            $this->id = $values['id'] ?? '';
            $this->name = $values['name'] ?? '';
            $this->description = $values['description'] ?? '';
            $this->userId = $values['userId'] ?? '';
            $this->habitType = $values['habitType'] ?? 0;
            $this->minValue = $values['minValue'] ?? null;
            $this->maxValue = $values['maxValue'] ?? null;
            $this->enumValues = $values['enumValues'] ?? null;
        }
    }
}