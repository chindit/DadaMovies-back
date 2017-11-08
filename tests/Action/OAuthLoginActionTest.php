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
    private $tokenManager;
    private $jwtManager;
    private $googleClientWrapper;

    protected function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();

        $this->tokenManager = $this->prophet->prophesize(TokenManager::class);
        $this->jwtManager = $this->prophet->prophesize(JWTManager::class);
        $this->googleClientWrapper = $this->prophet->prophesize(GoogleClientWrapper::class);
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
        $this->googleClientWrapper->verifyIdToken(Argument::any())->willReturn(false);

        $this->oAuthLoginAction = new OAuthLoginAction($this->tokenManager->reveal(), $this->jwtManager->reveal(), $this->googleClientWrapper->reveal());

        $oauth = new OAuth();
        $oauth->setIdToken('bépo');
        $this->oAuthLoginAction->__invoke($oauth);
    }

    public function testValidKeys()
    {
        $tokenData = ['sub' => 'bus', 'picture' => 'erutcip', 'name' => 'eman', 'exp' => 'pxe', 'iat' => 'tai', 'email' => 'liame', 'token' => 'nekot'];

        $user = new User();
        $user->setId(32);
        $submittedData = $tokenData;
        $submittedData['token'] = 'DadaMovies';
        $this->tokenManager->handleOAuthUser($submittedData)->willReturn($user);
        $this->tokenManager->handleOAuthUser($submittedData)->shouldBeCalled();
        $this->jwtManager->create($user)->shouldBeCalled();
        $this->jwtManager->create($user)->willReturn('JWTToken');
        $this->googleClientWrapper->verifyIdToken(Argument::any())->willReturn($tokenData);

        $this->oAuthLoginAction = new OAuthLoginAction($this->tokenManager->reveal(), $this->jwtManager->reveal(), $this->googleClientWrapper->reveal());

        $oauth = new OAuth();
        $oauth->setIdToken('DadaMovies');
        $response = $this->oAuthLoginAction->__invoke($oauth);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(new JsonResponse('JWTToken'), $response);
    }
}
