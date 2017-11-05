<?php
declare(strict_types=1);

namespace App\Tests\Action;


use App\Action\OAuthLoginAction;
use App\Entity\OAuth;
use App\Entity\User;
use App\Service\GoogleClientWrapper;
use App\Service\TokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;
use Symfony\Component\HttpFoundation\JsonResponse;

class OAuthLoginActionTest extends TestCase
{
    /** @var  Prophet */
    private $prophet;
    private $oAuthLoginAction;

    protected function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

    /**
     * @expectedException App\Exception\OAuthException
     * @expectedExceptionMessage Social network token is invalid
     */
    public function testMissingKeys()
    {
        $tokenManager = $this->prophet->prophesize(TokenManager::class)->reveal();
        $jwtManager = $this->prophet->prophesize(JWTManager::class)->reveal();
        $googleClientWrapper = $this->prophet->prophesize(GoogleClientWrapper::class);
        $googleClientWrapper->verifyIdToken(Argument::any())->willReturn(false);

        $this->oAuthLoginAction = new OAuthLoginAction($tokenManager, $jwtManager, $googleClientWrapper->reveal());

        $oauth = new OAuth();
        $oauth->setIdToken('bépo');
        $this->oAuthLoginAction->__invoke($oauth);
    }

    public function testValidKeys()
    {
        $tokenData = ['sub' => 'bus', 'picture' => 'erutcip', 'name' => 'eman', 'exp' => 'pxe', 'iat' => 'tai', 'email' => 'liame', 'token' => 'nekot'];
        $tokenManager = $this->prophet->prophesize(TokenManager::class);
        $user = new User();
        $user->setId(32);
        $submittedData = $tokenData;
        $submittedData['token'] = 'DadaMovies';
        $tokenManager->handleOAuthUser($submittedData)->willReturn($user);
        $tokenManager->handleOAuthUser($submittedData)->shouldBeCalled();

        $jwtManager = $this->prophet->prophesize(JWTManager::class);
        $jwtManager->create($user)->shouldBeCalled();
        $jwtManager->create($user)->willReturn('JWTToken');
        $googleClientWrapper = $this->prophet->prophesize(GoogleClientWrapper::class);
        $googleClientWrapper->verifyIdToken(Argument::any())->willReturn($tokenData);

        $this->oAuthLoginAction = new OAuthLoginAction($tokenManager->reveal(), $jwtManager->reveal(), $googleClientWrapper->reveal());

        $oauth = new OAuth();
        $oauth->setIdToken('DadaMovies');
        $response = $this->oAuthLoginAction->__invoke($oauth);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(new JsonResponse('JWTToken'), $response);
    }
}