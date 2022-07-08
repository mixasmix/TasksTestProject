<?php

namespace App\Enum;

class TaskPriority extends Enum
{
    /**
     * Низкий приоритет
     */
    public const LOW = 'low';

    /**
     * Средний приоритет
     */
    public const MEDIUM = 'medium';

    /**
     * Высокий приоритет
     */
    public const HIGH = 'high';

    /**
     * Допустимые значения
     */
    public const VALID_VALUES = [
        self::LOW,
        self::MEDIUM,
        self::HIGH,
    ];

    /**
     * @return array
     */
    protected function getValidValues(): array
    {
        return self::VALID_VALUES;
    }

    /**
     * @return TaskPriority
     */
    public static function low(): self
    {
        return new self(self::LOW);
    }

    /**
     * @return TaskPriority
     */
    public static function medium(): self
    {
        return new self(self::MEDIUM);
    }

    /**
     * @return TaskPriority
     */
    public static function high(): self
    {
        return new self(self::HIGH);
    }
}
