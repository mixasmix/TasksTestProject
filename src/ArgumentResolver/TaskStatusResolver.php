<?php

namespace App\ArgumentResolver;

use App\Enum\TaskStatus;
use App\VAlidation\TaskStatusValidator;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class TaskStatusResolver implements ArgumentValueResolverInterface
{
    /**
     * @param TaskStatusValidator $validator
     */
    public function __construct(private TaskStatusValidator $validator)
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
        return TaskStatus::class === $argument->getType();
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
        $taskStatus = $request->get('task_status');

        $errors = $this->validator->validate([
            'task_status' => $taskStatus,
        ]);

        if (!empty($errors)) {
            throw new Exception(
                sprintf(
                    'Ошибка валидации: %s',
                    implode(', ', $errors),
                ),
            );
        }

        yield new TaskStatus($taskStatus);
    }
}
