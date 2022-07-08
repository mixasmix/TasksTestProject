<?php

namespace App\Types;

use App\Enum\TaskStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

class TaskStatusType extends Type
{
    /**
     * Имя типа данных
     */
    private const TYPE_NAME = 'task_status_type';

    /**
     * @param array            $column
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    /**
     * @param string | null    $value
     * @param AbstractPlatform $platform
     *
     * @return TaskStatus | null
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?TaskStatus
    {
        return empty($value) ? null : new TaskStatus($value);
    }

    /**
     * @param TaskStatus | null $value
     * @param AbstractPlatform  $platform
     *
     * @return string | null
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null == $value) {
            return null;
        }

        if (!$value instanceof TaskStatus) {
            throw new InvalidArgumentException(
                sprintf('Недопустимый тип значения `%s`', $this->getName()),
            );
        }

        return $value->getValue();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::TYPE_NAME;
    }
}
