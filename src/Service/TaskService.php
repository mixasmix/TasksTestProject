<?php

namespace App\Service;

use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Throwable;

class TaskService extends AbstractService
{
    /**
     * @param string       $taskName
     * @param User         $user
     * @param TaskPriority $priority
     * @param array        $tags
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

    /**
     * @param Task $task
     * @param Tag  $tag
     *
     * @return Task
     */
    public function removeTag(Task $task, Tag $tag): Task
    {
        $task->removeTag($tag);

        $this->entityManager->flush();

        return $task;
    }
}
