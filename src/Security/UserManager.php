<?php

namespace App\Security;


use App\Entity\Token;
use App\Entity\User;
use App\Exception\OAuthException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserManager
{
    /**
     * @var UserPasswordEncoder
     */
    private $encoder;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(UserPasswordEncoder $encoder, EntityManager $entityManager)
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }

    public function registerUser(User $user): User
    {
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        $user->setPremium(false);
        $user->addRole('ROLE_USER');

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        $requiredKeys = ['picture', 'name', 'locale', 'email'];

        if (!empty(array_diff($requiredKeys, array_keys($data)))) {
            throw new OAuthException('Missing required keys');
        }

        $user->setLocale($data['locale']);
        $user->setUsername($data['email']);
        $user->setName($data['name']);
        $user->setProfilePicture($data['picture']);

        return $user;
    }

    public function findByTokenOrEmail(Token $token, string $email): ?User
    {
        /** @var Token $activeToken */
        $activeToken = $this->entityManager->getRepository(Token::class)->findOneBy(['userId' => $token->getUserId()], ['id' => 'desc']);

        return ($activeToken) ? $activeToken->getUser() : $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }
}
