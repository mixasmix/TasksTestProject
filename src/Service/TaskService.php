<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Throwable;

class TaskService extends AbstractService
{
    /**
     * @param string $taskName
     *
     * @return Task
     *
     * @throws Throwable
     */
    public function create(
        string $taskName,
        User $user,
        TaskPriority $priority,
        array $tags,
    ): Task {
        $this->entityManager->beginTransaction();

        try {
            $task = new Task(
                name: $taskName,
                tags: $tags,
                author: $user,
                status: TaskStatus::work(),
                priority: $priority,
            );

            $this->entityManager->persist($task);

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();

            throw $exception;
        }

        return $task;
    }
}
