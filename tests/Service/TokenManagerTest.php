<?php
declare(strict_types=1);

namespace App\Tests\Service;


use App\Entity\Token;
use App\Entity\User;
use App\Service\TokenManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
    private $tokenRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();
        $this->entityManager = $this->prophet->prophesize(EntityManager::class);
        $this->userManager = $this->prophet->prophesize(UserManager::class);

        $this->tokenRepository = $this->prophet->prophesize(EntityRepository::class);
        $this->entityManager->getRepository(Token::class)->willReturn($this->tokenRepository);

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

        $user = new User();
        $user->setId(74);
        $user->setPassword('aaa');

        $this->userManager->updateUser(Argument::any(), Argument::any())->willReturn($user);
        $this->userManager->findByTokenOrEmail(Argument::any(), 'liame')->willReturn($user);
        $this->entityManager->persist(Argument::any())->willReturn(true);
        $this->entityManager->flush()->willReturn(true);
        $this->tokenRepository->findBy(['userId' => 12])->willReturn(null);


        $data = ['sub' => 12, 'picture' => 'erutcip', 'name' => 'eman', 'exp' => 123, 'iat' => 321, 'email' => 'liame', 'token' => 'DadaMovies'];
        $result = $this->tokenManager->handleOAuthUser($data);

        $this->userManager->findByTokenOrEmail(Argument::any(), 'liame')->shouldBeCalled();
        $this->entityManager->persist($user->setProfilePicture('erutcip')->setName('eman')->setUsername('liame'))->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->assertEquals($user, $result);
    }

    public function testValidCallWithNoPasswordAndOldTokens()
    {
        $token = new Token();
        $token->setUserId(12);
        $token->setExpiration(123);
        $token->setIssued(321);
        $token->setToken('DadaMovies');

        $user = new User();
        $user->setId(74);

        $this->userManager->findByTokenOrEmail(Argument::any(), 'liame')->willReturn(null);
        $this->userManager->updateUser(Argument::any(), Argument::any())->willReturn($user);
        $this->userManager->registerUser(Argument::which('id', 74))->willReturn($user);
        $this->entityManager->persist(Argument::any())->willReturn(true);
        $this->entityManager->remove(Argument::type(Token::class))->willReturn(true);
        $this->entityManager->flush()->willReturn(true);
        $expiredToken = $token->setExpiration(time()-3600)->setId(1);
        $validToken = clone $token;
        $validToken = $validToken->setExpiration(time()+3600)->setId(2);
        $this->tokenRepository->findBy(['userId' => 12])->willReturn([$expiredToken, $validToken]);

        $data = ['sub' => 12, 'picture' => 'erutcip', 'name' => 'eman', 'exp' => 123, 'iat' => 321, 'email' => 'liame', 'token' => 'DadaMovies'];
        $result = $this->tokenManager->handleOAuthUser($data);

        $this->userManager->registerUser(Argument::which('id', 74))->shouldHaveBeenCalled();
        $this->entityManager->remove($expiredToken)->shouldHaveBeenCalled();
        $this->entityManager->remove($validToken)->shouldNotBeCalled();
        $this->entityManager->flush()->shouldHaveBeenCalled();

        $this->assertNotEmpty($result->getPassword());
    }
}
