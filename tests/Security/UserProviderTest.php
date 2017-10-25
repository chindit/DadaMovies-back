<?php
declare(strict_types=1);

use App\Entity\User;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProviderTest extends TestCase
{
    /** @var  Prophet */
    private $prophet;
    private $entityManager;
    private $repository;
    private $userProvider;
    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = new User('phpunit');

        $this->prophet = new Prophet();
        $this->entityManager = $this->prophet->prophesize(EntityManager::class);
        $this->repository = $this->prophesize(EntityRepository::class);
        $this->repository->findOneBy(['username' => 'phpunit'])
            ->willReturn($this->user);
        $this->entityManager->getRepository(User::class)->willReturn(
            $this->repository->reveal()
        );

        $this->userProvider = new UserProvider($this->entityManager->reveal());
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

    public function testSupportsClassWithValidClass()
    {
        $this->assertTrue($this->userProvider->supportsClass(User::class));
    }

    public function testSupportsClassWithInvalidClass()
    {
        $this->assertFalse($this->userProvider->supportsClass(UserProvider::class));
    }

    public function testLoadByUsernameWithValidUsername()
    {
        $this->assertEquals($this->user, $this->userProvider->loadUserByUsername('phpunit'), 'Returned user is not valid');
    }

    public function testLoadByUsernameWithInvalidUsername()
    {
        $this->repository = $this->prophesize(EntityRepository::class);
        $this->repository->findOneBy(['username' => 'phpunit'])
            ->willReturn(null);
        $this->entityManager->getRepository(User::class)->willReturn(
            $this->repository->reveal()
        );

        $this->userProvider = new UserProvider($this->entityManager->reveal());

        $this->expectException(UsernameNotFoundException::class);

        $this->userProvider->loadUserByUsername('phpunit');

        $this->assertEquals('Username "phpunit" does not exist.', $this->getExpectedExceptionMessage(), 'Invalid exception message');
    }

    public function testRefreshUserWithValidUser()
    {
        $this->repository->findOneBy(['username' => 'phpunit'])->shouldBeCalled();

        $this->userProvider->refreshUser($this->user);
    }

    public function testRefreshUserWithInvalidUser()
    {
        $this->expectException(UnsupportedUserException::class);

        $this->userProvider->refreshUser(new DummyClass());

        $this->assertEquals('Instances of "DummyClass" are not supported.', $this->getExpectedExceptionMessage(), 'Invalid exception message');
    }
}

// Used for tests
class DummyClass implements UserInterface{
    public function getUsername()
    {
        return '';
    }

    public function getPassword()
    {
        return '';
    }

    public function eraseCredentials()
    {
        return;
    }

    public function getRoles()
    {
        return [];
    }

    public function getSalt()
    {
        return '';
    }
}