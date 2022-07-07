<?php

namespace App\Validation;

use App\Enum\TaskStatus;
use Symfony\Component\Validator\Constraints as Assert;

class TaskStatusValidator extends AbstractValidator
{
    /**
     * @return array
     */
    protected function getConstraints(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getOptionalFields(): array
    {
        return [];
    }

    /**
     * @return array
     */
    private function getStatusRules(): array
    {
        return [
            $this->getNotBlank(),
            new Assert\Choice([
                'value' => TaskStatus::VALID_VALUES,
                'message' => 'Недопустимое значение статуса',
            ]),
        ];
    }
}
