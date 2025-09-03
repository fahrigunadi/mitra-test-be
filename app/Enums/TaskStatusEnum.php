<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::DONE => 'Done',
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::TODO => 'todo',
            self::IN_PROGRESS => 'in_progress',
            self::DONE => 'done',
        };
    }

    public static function values(): array
    {
        return [
            self::TODO->value(),
            self::IN_PROGRESS->value(),
            self::DONE->value(),
        ];
    }
}
