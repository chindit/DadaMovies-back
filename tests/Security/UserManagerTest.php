<?php
declare(strict_types=1);

use App\Entity\User;
use App\Security\UserManager;
use PHPUnit\Framework\TestCase;
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
    /** @var  UserManager */
    private $userManager;

    protected function setUp()
    {
        parent::setUp();

        $this->user = new User('phpunit', 'password');

        $this->prophet = new Prophet();
        $this->passwordEncoder = $this->prophet->prophesize(UserPasswordEncoder::class);
        $this->passwordEncoder->encodePassword($this->user, 'password')->willReturn('drowssap');

        $this->userManager = new UserManager($this->passwordEncoder->reveal());
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
}
