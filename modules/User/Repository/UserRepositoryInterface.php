<?php

namespace Modules\User\Repository;

use Modules\User\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    public function delete(string $id): void;
}