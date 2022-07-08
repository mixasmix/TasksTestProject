<?php

namespace App\ArgumentResolver;

use App\DTO\CreateUserData;
use App\Validation\CreateUserDataValidator;
use Exception;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CreateUserDataResolver implements ArgumentValueResolverInterface
{
    /**
     * @param CreateUserDataValidator $validator
     */
    public function __construct(private CreateUserDataValidator $validator)
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
        return CreateUserData::class === $argument->getType();
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
        ]);

        if (!empty($errors)) {
            throw new Exception(
                sprintf(
                    'Ошибка валидации: %s',
                    implode(', ', $errors),
                ),
            );
        }

        yield new CreateUserData($params['name']);
    }
}
