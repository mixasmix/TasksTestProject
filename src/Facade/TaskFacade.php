<?php

namespace App\Facade;

use App\DTO\CreateTaskData;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TagRepository;
use App\Service\TagService;
use App\Service\TaskService;
use Doctrine\ORM\NonUniqueResultException;
use Throwable;

class TaskFacade
{
    /**
     * @param TaskService   $taskService
     * @param TagRepository $tagRepository
     * @param TagService    $tagService
     */
    public function __construct(
        private TaskService $taskService,
        private TagRepository $tagRepository,
        private TagService $tagService
    ) {
    }

    /**
     * @param CreateTaskData $data
     * @param User           $user
     *
     * @return Task
     *
     * @throws Throwable
     */
    public function create(CreateTaskData $data, User $user): Task
    {
        $tags = $this->getTags($data->getTags(), $user);

        $task = $this->taskService->create(
            taskName: $data->getName(),
            user: $user,
            priority: $data->getPriority(),
            tags: $tags,
        );

        //связываем теги с таском
        array_map(
            fn (Tag $tag): Tag => $this->tagService->addTask(tag: $tag, task: $task),
            $tags,
        );

        return $task;
    }

    /**
     * @param array $tags
     * @param User  $user
     *
     * @return array<Tag>
     *
     * @throws NonUniqueResultException
     * @throws Throwable
     */
    private function getTags(array $tags, User $user): array
    {
        return array_map(
            function (string $tagName) use ($user): Tag {
                $tag = $this->tagRepository->findByName($tagName);

                if (is_null($tag)) {
                    //если такого тега нет, то создаем его
                    $tag = $this->tagService->create(
                        tagName: $tagName,
                        user: $user,
                    );
                }

                return $tag;
            },
            $tags,
        );
    }
}
