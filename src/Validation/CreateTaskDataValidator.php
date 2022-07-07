<?php

namespace App\Validation;

use App\Enum\TaskPriority;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskDataValidator extends AbstractValidator
{
    /**
     * @return array
     */
    protected function getConstraints(): array
    {
        return [
            'name' => $this->getStringRules(),
            'priority' => $this->getPriorityRules(),
            'tags' => $this->getTagsRules(),
        ];
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
    private function getPriorityRules(): array
    {
        return [
            $this->getNotBlank(),
            new Assert\Choice([
                'value' => TaskPriority::VALID_VALUES,
                'message' => 'Недопустимое значение приоритета',
            ]),
        ];
    }

    /**
     * @return array
     */
    private function getTagsRules(): array
    {
        return [
            $this->getNotBlank(),
            new Assert\All([
                new Assert\Uuid(),
            ]),
        ];
    }
}
