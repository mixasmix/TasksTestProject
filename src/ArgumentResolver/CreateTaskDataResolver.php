<?php

namespace App\ArgumentResolver;

use App\DTO\CreateTaskData;
use App\Enum\TaskPriority;
use App\Validation\CreateTaskDataValidator;
use Exception;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CreateTaskDataResolver implements ArgumentValueResolverInterface
{
    /**
     * @param CreateTaskDataValidator $validator
     */
    public function __construct(private CreateTaskDataValidator $validator)
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
        return CreateTaskData::class === $argument->getType();
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return iterable
     *
     * @throws JsonException
     * @throws Exception
     */
    public function resolve(
        Request $request,
        ArgumentMetadata $argument
    ): iterable {
        $params = json_decode(
            json: $request->getContent(),
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );

        $errors = $this->validator->validate([
            'name' => $params['name'],
            'priority' => $params['priority'],
            'tags' => $params['tags'],
        ]);

        if (!empty($errors)) {
            throw new Exception(
                sprintf(
                    'Ошибка валидации: %s',
                    implode(', ', $errors),
                ),
            );
        }

        yield new CreateTaskData(
            priority: new TaskPriority($params['priority']),
            name: $params['name'],
            tags: $params['tags'],
        );
    }
}
