<?php

namespace App\Validation;

use App\Enum\TaskPriority;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidator
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var array
     */
    protected array $validationRules = [];

    /**
     * @var array
     */
    protected array $optionalFields;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->optionalFields = $this->getOptionalFields();
    }

    /**
     * Возвращает правила валидации
     *
     * @return array
     */
    abstract protected function getConstraints(): array;

    /**
     * Возвращает список необязательных полей
     *
     * @return array
     */
    abstract protected function getOptionalFields(): array;

    /**
     * Валидирует данные и возвращает массив с ошибками (или пустой массив, если ошибок нет)
     *
     * @param array $requestFields
     *
     * @return array
     */
    public function validate(array $requestFields): array
    {
        // Установка кастомных правил валидации вместо дефолтных, если первые были заданы
        $constraints = array_merge($this->getConstraints(), $this->validationRules);

        // Удаление правил валидации для необязательных полей, которые не пришли
        $constraints = array_filter(
            $constraints,
            function (string $fieldName) use ($requestFields): bool {
                if (!in_array($fieldName, $this->optionalFields)) {
                    return true;
                }

                return key_exists($fieldName, $requestFields);
            },
            ARRAY_FILTER_USE_KEY
        );

        $errors = [];

        /**
         * @var ConstraintViolation $violation
         */
        foreach ($this->validator->validate($requestFields, new Collection($constraints)) as $violation) {
            $field = preg_replace(['/\]\[/', '/\[|\]/'], ['.', ''], $violation->getPropertyPath());
            $errors[$field] = $violation->getMessage();
        }

        return $errors;
    }

    /**
     * Изменение правил валидации для указанного в $fieldName поля
     *
     * @param array                $fieldsName
     * @param array | Constraint[] $constraints
     *
     * @return void
     */
    public function changeDefaultValidationRule(array $fieldsName, array $constraints): void
    {
        foreach ($fieldsName as $fieldName) {
            $this->validationRules[$fieldName] = $constraints;
        }
    }

    /**
     * Добавляет переданные названия полей в число необязательных
     *
     * @param array | string[] $fields
     *
     * @return void
     */
    public function addOptionalFields(array $fields): void
    {
        $this->optionalFields = array_merge($this->optionalFields, $fields);
    }

    /**
     * Удаляет переданные названия полей из числа необязательных
     * Если названия полей не переданы, удаляет все необязательные поля
     *
     * @param array | string[] | null $fields
     *
     * @return void
     */
    public function removeOptionalFields(?array $fields): void
    {
        if (empty($fields)) {
            $this->optionalFields = [];

            return;
        }

        array_walk($fields, function (string $field): void {
            $key = array_search($field, $this->optionalFields);

            if (false !== $key) {
                unset($this->optionalFields[$key]);
            }
        });
    }

    /**
     * @return Assert\NotBlank
     */
    protected function getNotBlank(): Assert\NotBlank
    {
        return new Assert\NotBlank(['message' => 'Поле обязательно к заполнению']);
    }

    /**
     * Возвращает правила валидации id
     *
     * @return array
     */
    protected function getIdRules(): array
    {
        return [
            $this->getNotBlank(),
            new Assert\Range([
                'min' => 1,
                'minMessage' => 'ID не может быть меньше 1',
            ]),
            new Assert\Regex([
                'pattern' => "/^[0-9]+$/",
                'message' => 'ID должен быть целым числом',
            ]),
        ];
    }

    /**
     * @return array
     */
    protected function getRequiredPhoneNumberRules(): array
    {
        return [
            new Assert\Regex([
                'pattern' => '/^7\d{10}$/',
                'message' => 'Номер телефона должен состоять из 11 цифр',
            ]),
        ];
    }

    /**
     * @return array
     */
    protected function getPasswordRules(): array
    {
        return [
            $this->getNotBlank(),
            new Assert\Type([
                'type' => 'string',
                'message' => 'Значение должно быть строкой'
            ]),
            new Assert\Regex([
                'pattern' => '/^([A-Za-z]|\d){6,}$/',
                'message' => 'От 6 латинских букв или цифр',
            ]),
        ];
    }

    /**
     * @return array
     */
    protected function getStringRules(): array
    {
        return [
            $this->getNotBlank(),
            new Assert\Type([
                'type' => 'string',
                'message' => 'Значение должно быть строкой'
            ]),
        ];
    }

    /**
     * @return array
     */
    protected function getPriorityRules(): array
    {
        return [
            $this->getNotBlank(),
            new Assert\Choice([
                'value' => TaskPriority::VALID_VALUES,
                'message' => 'Недопустимое значение приоритета',
            ]),
        ];
    }
}
