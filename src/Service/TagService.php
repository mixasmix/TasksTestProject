<?php

namespace App\Service;

use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Throwable;

class TagService extends AbstractService
{
    /**
     * @param Task         $task
     * @param TaskPriority $priority
     *
     * @return Task
     */
    public function changePriority(
        Task $task,
        TaskPriority $priority
    ): Task {
        $task->updatePriority($priority);

        $this->entityManager->flush();

        return $task;
    }

    /**
     * @param string $tagName
     * @param User   $user
     *
     * @return Tag
     *
     * @throws Throwable
     */
    public function create(
        string $tagName,
        User $user
    ): Tag {
        $this->entityManager->beginTransaction();

        try {
            $tag = new Tag(
                name: $tagName,
                author: $user,
            );

            $this->entityManager->persist($tag);

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();

            throw $exception;
        }

        return $tag;
    }

    /**
     * @param Task       $task
     * @param TaskStatus $status
     *
     * @return Task
     */
    public function updateStatus(
        Task $task,
        TaskStatus $status
    ): Task {
        $task->updateStatus($status);

        $this->entityManager->flush();

        return $task;
    }
}
