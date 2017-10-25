<?php
declare(strict_types=1);

use App\Action\UserAction;
use App\Entity\User;
use App\Security\UserManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class UserActionTest extends TestCase
{
    /** @var  Prophet */
    private $prophet;
    /** @var  UserManager */
    private $userManager;
    /** @var  UserAction */
    private $userAction;


    protected function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();

        $this->userManager = $this->prophet->prophesize(UserManager::class);
        $this->userAction = new UserAction($this->userManager->reveal());
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

    public function testIsServiceCalled()
    {
        $user = new User('pseudo', 'Pa$$w0rd');

        $this->userManager->registerUser($user)->willReturn($user);
        $this->userManager->registerUser($user)->shouldBeCalled();

        $this->userAction->__invoke($user);
    }
}