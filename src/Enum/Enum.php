<?php

namespace App\Enum;

use InvalidArgumentException;
use JetBrains\PhpStorm\Immutable;
use JsonSerializable;

#[Immutable]
abstract class Enum implements JsonSerializable
{
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        if (!in_array($value, $this->getValidValues(), true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Недопустимое значение объекта %s: %s',
                    static::class,
                    $value,
                ),
            );
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function isValueEqualTo(string $value): bool
    {
        return $this->value === $value;
    }

    /**
     * Возвращает допустимые значения объекта
     *
     * @return array
     */
    abstract protected function getValidValues(): array;

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->getValue();
    }
}
