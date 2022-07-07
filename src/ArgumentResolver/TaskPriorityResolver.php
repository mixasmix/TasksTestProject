<?php

namespace App\ArgumentResolver;

use App\Enum\TaskPriority;
use App\Validation\TaskPriorityValidator;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class TaskPriorityResolver implements ArgumentValueResolverInterface
{
    /**
     * @param TaskPriorityValidator $validator
     */
    public function __construct(private TaskPriorityValidator $validator)
    {
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     */
    public function supports(
        Request $request,
        ArgumentMetadata $argument
    ): bool {
        return TaskPriority::class === $argument->getType();
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return iterable
     *
     * @throws Exception
     */
    public function resolve(
        Request $request,
        ArgumentMetadata $argument
    ): iterable {
        $taskPriority = $request->get('task_priority');

        $errors = $this->validator->validate([
            'task_priority' => $taskPriority,
        ]);

        if (!empty($errors)) {
            throw new Exception(
                sprintf(
                    'Ошибка валидации: %s',
                    implode(', ', $errors),
                ),
            );
        }

        yield new TaskPriority($taskPriority);
    }
}
