<?php
declare(strict_types=1);

namespace App\Service;


use App\Entity\Token;
use App\Entity\User;
use App\Exception\OAuthException;
use App\Security\UserManager;
use Doctrine\ORM\EntityManager;

class TokenManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(EntityManager $entityManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }

    public function handleOAuthUser(array $data): User
    {
        $token = $this->hydrateToken($data);

        $user = $this->userManager->findByTokenOrEmail($token, $data['email']) ?? new User();
        $token->setUser($user);
        $user = $this->userManager->updateUser($user, $data);

        // If it's a new user, assign a random password to pass validation
        $user->setPassword(bin2hex(openssl_random_pseudo_bytes(25)));
        $user = $this->userManager->registerUser($user);

        $this->cleanOldTokens($token);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function hydrateToken(array $data): Token
    {
        $token = new Token();
        $requiredKeys = ['sub', 'picture', 'name', 'locale', 'exp', 'iat', 'email', 'token'];

        if (!empty(array_diff($requiredKeys, array_keys($data)))) {
            throw new OAuthException('Missing required keys');
        }

        $token->setExpiration($data['exp']);
        $token->setIssued($data['iat']);
        $token->setToken($data['token']);
        $token->setUserId($data['sub']);

        return $token;
    }

    private function cleanOldTokens(Token $token): void
    {
        $oldTokens = $this->entityManager->getRepository(Token::class)->findBy(['userId' => $token->getUserId()]);

        /** @var Token $currentToken */
        foreach ($oldTokens as $currentToken) {
            if ($currentToken->getExpiration() < time()) {
                $this->entityManager->remove($currentToken);
            }
        }

        $this->entityManager->flush();
    }
}
