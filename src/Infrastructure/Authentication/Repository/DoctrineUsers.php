<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Authentication\Repository;

use Caloriary\Authentication\Exception\UserNotFound;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\EmailAddress;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

final class DoctrineUsers implements Users
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var ObjectRepository
     */
    private $repository;


    public function __construct(ObjectManager $manager, ObjectRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }


    /**
     * @throws UserNotFound
     */
    public function get(EmailAddress $emailAddress): User
    {
        $user = $this->repository->find($emailAddress);

        if ($user instanceof User) {
            return $user;
        }

        throw new UserNotFound();
    }


    public function add(User $user): void
    {
        $this->manager->persist($user);
        $this->manager->flush();
    }


    public function remove(User $user): void
    {
        $this->manager->remove($user);
        $this->manager->flush();
    }
}
