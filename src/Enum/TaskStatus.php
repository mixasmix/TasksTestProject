<?php

namespace App\Enum;

class TaskStatus extends Enum
{
    /**
     * В работе
     */
    public const WORK = 'work';

    /**
     * Завершена
     */
    public const CANCELLED = 'cancelled';

    /**
     * Допустимые значения
     */
    public const VALID_VALUES = [
        self::WORK,
        self::CANCELLED,
    ];

    /**
     * @return array
     */
    protected function getValidValues(): array
    {
        return self::VALID_VALUES;
    }

    /**
     * @return TaskStatus
     */
    public static function work(): self
    {
        return new self(self::WORK);
    }

    /**
     * @return TaskStatus
     */
    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }
}
