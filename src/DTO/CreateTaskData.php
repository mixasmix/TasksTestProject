<?php

namespace App\DTO;

use App\Enum\TaskPriority;

class CreateTaskData
{
    /**
     * @param TaskPriority $priority
     * @param string       $name
     * @param array        $tags
     */
    public function __construct(private TaskPriority $priority, private string $name, private array $tags)
    {
    }

    /**
     * @return TaskPriority
     */
    public function getPriority(): TaskPriority
    {
        return $this->priority;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
