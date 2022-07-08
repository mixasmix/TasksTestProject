<?php

namespace App\Validation;

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
    private function getTagsRules(): array
    {
        return [
            $this->getNotBlank(),
            new Assert\All(
                $this->getStringRules(),
            ),
        ];
    }
}
