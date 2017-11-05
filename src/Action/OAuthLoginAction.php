<?php
declare(strict_types=1);

namespace App\Action;


use App\Entity\OAuth;
use App\Exception\OAuthException;
use App\Service\GoogleClientWrapper;
use App\Service\UserManager;
use App\Service\TokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class OAuthLoginAction
{
    private $googleClient;
    /**
     * @var TokenManager
     */
    private $tokenManager;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var JWTManager
     */
    private $JWTManager;

    public function __construct(TokenManager $tokenManager, UserManager $userManager, JWTManager $JWTManager, GoogleClientWrapper $googleClientWrapper)
    {
        $this->googleClient = $googleClientWrapper;
        $this->tokenManager = $tokenManager;
        $this->userManager = $userManager;
        $this->JWTManager = $JWTManager;
    }

    /**
     * @Route(
     *     name="api_login_oauth",
     *     path="/users/oauth",
     *     defaults={"_api_resource_class"=OAuth::class, "_api_collection_operation_name"="api_login_oauth"}
     * )
     * @Method("POST")
     * @param OAuth $data
     * @return JsonResponse
     * @throws OAuthException
     */
    public function __invoke(OAuth $data): JsonResponse
    {
        $payload = $this->googleClient->verifyIdToken($data->getIdToken());
        if ($payload) {
            $payload['token'] = $data->getIdToken();
            $user = $this->tokenManager->handleOAuthUser($payload);

            return new JsonResponse($this->JWTManager->create($user));
        } else {
            throw new OAuthException('a');
        }
    }
}
