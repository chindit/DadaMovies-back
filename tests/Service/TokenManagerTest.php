<?php
declare(strict_types=1);

namespace App\Tests\Service;


use App\Entity\Token;
use App\Entity\User;
use App\Service\TokenManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;

class TokenManagerTest extends TestCase
{
    /** @var  Prophet */
    private $prophet;
    /** @var  TokenManager */
    private $tokenManager;
    private $entityManager;
    private $userManager;

    protected function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();
        $this->entityManager = $this->prophet->prophesize(EntityManager::class);
        $this->userManager = $this->prophet->prophesize(UserManager::class);

        $this->tokenManager = new TokenManager($this->entityManager->reveal(), $this->userManager->reveal());
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

    /**
     * @expectedException App\Exception\OAuthException
     * @expectedExceptionMessage Missing required keys
     */
    public function testInvalidArray()
    {
        $this->tokenManager->handleOAuthUser(['empty' => 'data']);
    }

    public function testValidCall()
    {
        $token = new Token();
        $token->setUserId(12);
        $token->setExpiration(123);
        $token->setIssued(321);
        $token->setToken('DadaMovies');

        $this->userManager->findByTokenOrEmail($token, 'liame')->shouldBeCalled();
        $this->userManager->findByTokenOrEmail($token, 'liame')->willReturn(null);

        $user = new User();
        $user->setId(74);
        $this->userManager->updateUser(Argument::any())->willReturn($user);

        $data = ['sub' => 12, 'picture' => 'erutcip', 'name' => 'eman', 'exp' => 123, 'iat' => 321, 'email' => 'liame', 'token' => 'DadaMovies'];
        $this->tokenManager->handleOAuthUser($data);
    }
}