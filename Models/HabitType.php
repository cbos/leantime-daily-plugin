<?php

namespace Leantime\Plugins\Daily\Models;

class HabitType
{
    public mixed $id = null;

    public ?string $name = '';

    public ?string $template = '';

    public function __construct(array|bool $values = false)
    {
        if ($values !== false) {
            $this->id = $values['id'] ?? '';
            $this->name = $values['name'] ?? '';
            $this->template = $values['template'] ?? '';
        }
    }
}