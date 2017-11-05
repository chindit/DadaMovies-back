<?php
declare(strict_types=1);

use App\Entity\Token;
use App\Entity\User;
use App\Service\UserManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserManagerTest extends TestCase
{
    /** @var  Prophet */
    private $prophet;
    /** @var  User */
    private $user;
    /** @var  UserPasswordEncoder */
    private $passwordEncoder;
    /** @var  EntityManager */
    private $entityManager;
    /** @var  UserManager */
    private $userManager;
    /** @var  EntityRepository */
    private $tokenRepository;
    /** @var  EntityRepository */
    private $userRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->user = new User('phpunit', 'password');

        $this->prophet = new Prophet();
        $this->passwordEncoder = $this->prophet->prophesize(UserPasswordEncoder::class);
        $this->passwordEncoder->encodePassword($this->user, 'password')->willReturn('drowssap');
        $this->entityManager = $this->prophet->prophesize(EntityManager::class);

        $this->tokenRepository = $this->prophet->prophesize(EntityRepository::class);
        $this->userRepository = $this->prophet->prophesize(EntityRepository::class);

        $this->entityManager->getRepository(Token::class)->willReturn($this->tokenRepository);
        $this->entityManager->getRepository(User::class)->willReturn($this->userRepository);

        $this->userManager = new UserManager($this->passwordEncoder->reveal(), $this->entityManager->reveal());
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

    public function testRegisterUser()
    {
        $encodedUser = $this->userManager->registerUser($this->user);

        $this->assertEquals('drowssap', $encodedUser->getPassword());
    }

    public function testFindByTokenWithToken()
    {
        $tokenTest = new Token();
        $user = new User();
        $user->setId(32);
        $tokenTest->setUser($user);
        $this->tokenRepository->findOneBy(['userId' => 23], ['id' => 'desc'])->willReturn($tokenTest);
        $this->userRepository->findOneBy(Argument::any())->willReturn('a');

        $this->tokenRepository->findOneBy(['userId' => 23], ['id' => 'desc'])->shouldBeCalled();
        $this->userRepository->findOneBy(Argument::any())->shouldNotBeCalled();

        $this->tokenRepository->reveal();
        $this->userRepository->reveal();

        $requestToken = new Token();
        $requestToken->setUserId(23);
        $result = $this->userManager->findByTokenOrEmail($requestToken, '');

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(32, $result->getId());
    }

    /**
     * @expectedException App\Exception\OAuthException
     * @expectedExceptionMessage Missing required keys
     */
    public function testUpdateUserFailure()
    {
        $wrongArray = ['picture' => 'erutcip', 'name' => 'eman', 'truc' => 'curt'];
        $this->userManager->updateUser(new User(), $wrongArray);
    }

    public function testCorrectUpdateUser()
    {
        $correctData = ['picture' => 'http://www.google.com', 'name' => 'John Smith', 'locale' => 'en', 'email' => 'john@smith.us'];

        $user = $this->userManager->updateUser(new User(), $correctData);

        $this->assertEquals('http://www.google.com', $user->getProfilePicture());
        $this->assertEquals('John Smith', $user->getName());
        $this->assertEquals('en', $user->getLocale());
        $this->assertEquals('john@smith.us', $user->getUsername());
    }
}
