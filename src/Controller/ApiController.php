<?php

namespace App\Controller;

use App\DTO\CreateTagData;
use App\DTO\CreateTaskData;
use App\DTO\CreateUserData;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Facade\TaskFacade;
use App\Repository\TagRepository;
use App\Service\TagService;
use App\Service\TaskService;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ApiController extends AbstractController
{
    /**
     * @param UserService   $userService
     * @param TagService    $tagService
     * @param TaskFacade    $taskFacade
     * @param TaskService   $taskService
     * @param TagRepository $tagRepository
     */
    public function __construct(
        private UserService $userService,
        private TagService $tagService,
        private TaskFacade $taskFacade,
        private TaskService $taskService,
        private TagRepository $tagRepository
    ) {
    }

    /**
     * @return JsonResponse
     */
    #[Route(path: '/')]
    public function index(): JsonResponse
    {
        return $this->json([
        ]);
    }

    /**
     * @param CreateUserData $userData
     *
     * @return JsonResponse
     *
     * @throws Throwable
     */
    #[Route(path: '/user', methods: ['POST'])]
    public function createUser(
        CreateUserData $userData
    ): JsonResponse {
        return $this->json([
            'data' => $this->userService->create($userData->getName()),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param CreateTagData $data
     * @param User          $user
     *
     * @return JsonResponse
     *
     * @throws Throwable
     */
    #[Route(path: '/user/{user_id}/tag', methods: ['POST'])]
    #[Entity(
        data: 'user',
        expr: 'repository.getById(user_id)',
    )]
    public function createTag(
        CreateTagData $data,
        User $user,
    ): JsonResponse {
        $tag = $this->tagRepository->findByName($data->getName());

        //чтобы не плодить одинаковые теги
        if (is_null($tag)) {
            $tag = $this->tagService->create(
                $data->getName(),
                $user,
            );
        }

        return $this->json([
            'data' => $tag,
        ], Response::HTTP_CREATED);
    }

    /**
     * @param CreateTaskData $data
     * @param User           $user
     *
     * @return JsonResponse
     *
     * @throws Throwable
     */
    #[Route(path: '/user/{user_id}/task', methods: ['POST'])]
    #[Entity(
        data: 'user',
        expr: 'repository.getById(user_id)',
    )]
    public function createTask(
        CreateTaskData $data,
        User $user,
    ): JsonResponse {
        return $this->json([
            'data' => $this->taskFacade->create(
                $data,
                $user,
            ),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param Task $task
     *
     * @return JsonResponse
     */
    #[Route(path: '/task/{task_id}', methods: ['GET'])]
    #[Entity(
        data: 'task',
        expr: 'repository.getById(task_id)',
    )]
    public function getTask(
        Task $task
    ): JsonResponse {
        return $this->json([
                'data' => $task,
        ]);
    }

    /**
     * @param Tag $tag
     *
     * @return JsonResponse
     */
    #[Route(path: '/tag/{tag_name}', methods: ['GET'])]
    #[Entity(
        data: 'tag',
        expr: 'repository.getByName(tag_name)',
    )]
    public function getTasksByTag(
        Tag $tag
    ): JsonResponse {
        return $this->json([
            'data' => $tag->getTasks()->map(
                //через ту эррей потому что иначе в рекурсию в jsonSerialize упадем
                fn (Task $task): array => $task->toArray(),
            )->toArray(),
        ]);
    }

    /**
     * @param Task       $task
     * @param TaskStatus $taskStatus
     *
     * @return JsonResponse
     */
    #[Route(path: '/task/{task_id}/status/{task_status}', methods: ['PUT'])]
    #[Entity(
        data: 'task',
        expr: 'repository.getById(task_id)',
    )]
    public function changeTaskStatus(
        Task $task,
        TaskStatus $taskStatus
    ): JsonResponse {
        return $this->json([
            'data' => $this->tagService->updateStatus(task: $task, status: $taskStatus),
        ]);
    }

    /**
     * @param Task         $task
     * @param TaskPriority $priority
     *
     * @return JsonResponse
     */
    #[Route(path: '/task/{task_id}/priority/{task_priority}', methods: ['PUT'])]
    #[Entity(
        data: 'task',
        expr: 'repository.getById(task_id)',
    )]
    public function changeTaskPriority(
        Task $task,
        TaskPriority $priority,
    ): JsonResponse {
        return $this->json([
            'data' => $this->tagService->changePriority(
                task: $task,
                priority: $priority,
            ),
        ]);
    }

    /**
     * @param Task $task
     * @param Tag  $tag
     *
     * @return JsonResponse
     */
    #[Route(path: '/task/{task_id}/tag/{tag_name}', methods: ['DELETE'])]
    #[Entity(
        data: 'task',
        expr: 'repository.getById(task_id)',
    )]
    #[Entity(
        data: 'tag',
        expr: 'repository.getByName(tag_name)',
    )]
    public function removeTaskTag(
        Task $task,
        Tag $tag
    ): JsonResponse {
        $this->taskService->removeTag(task: $task, tag: $tag);
        $this->tagService->removeTask(tag: $tag, task: $task);

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
