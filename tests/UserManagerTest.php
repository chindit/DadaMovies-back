<?php
declare(strict_types=1);

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Security\UserManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
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
    private $response;

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

    public function testIsSubscribed()
    {
        $this->assertEquals([KernelEvents::VIEW => ['registerUser', EventPriorities::POST_VALIDATE]], UserManager::getSubscribedEvents());
    }

    public function testRegisterUserWithInvalidClass()
    {
        // Any wrong object.
        $console = new \SebastianBergmann\Environment\Console();

        $response = $this->prophet->prophesize(GetResponseForControllerResultEvent::class);
        $response->getControllerResult()->willReturn($console);

        $revealedResponse = $response->reveal();

        $this->assertEquals($revealedResponse, $this->userManager->registerUser($revealedResponse));

        $response->setControllerResult(\Prophecy\Argument::any())->shouldNotBeCalled();
    }

    public function testRegisterUser()
    {
        $this->response = $this->prophet->prophesize(GetResponseForControllerResultEvent::class);
        $this->response->getControllerResult()->willReturn($this->user);

        $this->response->setControllerResult(\Prophecy\Argument::any())->shouldBeCalled();

        $encodedUser = $this->userManager->registerUser($this->response->reveal());

        $this->assertEquals('drowssap', $encodedUser->getControllerResult()->getPassword());
    }
}
