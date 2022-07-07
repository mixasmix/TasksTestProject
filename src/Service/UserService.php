<?php

namespace App\Service;

use App\Entity\User;
use Throwable;

class UserService extends AbstractService
{
    /**
     * @param string $userName
     *
     * @return User
     *
     * @throws Throwable
     */
    public function create(string $userName): User
    {
        $this->entityManager->beginTransaction();

        try {
            $user = new User($userName);

            $this->entityManager->persist($user);

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();

            throw $exception;
        }

        return $user;
    }
}
