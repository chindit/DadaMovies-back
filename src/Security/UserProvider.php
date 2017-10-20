<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function loadUserByUsername($username): User
    {
        /** @var User $userData */
        $userData = $this->repository->findOneBy(['username' => $username]);

        if ($userData) {
            return $userData;
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user): User
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
