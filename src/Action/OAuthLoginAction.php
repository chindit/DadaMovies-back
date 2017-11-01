<?php
declare(strict_types=1);

namespace App\Action;


use App\Entity\OAuth;
use App\Security\UserProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;

class OAuthLoginAction
{
    private $googleClient;

    public function __construct($googleClient)
    {
        $this->googleClient = $googleClient;
    }

    /**
     * @Route(
     *     name="api_login_oauth",
     *     path="/users/oauth",
     *     defaults={"_api_resource_class"=OAuth::class, "_api_collection_operation_name"="api_login_oauth"}
     * )
     * @Method("POST")
     */
    public function __invoke(OAuth $data): OAuth
    {
        $client = new \Google_Client(['client_id' => $this->googleClient]);
        $payload = $client->verifyIdToken($data->getIdToken());
        if ($payload) {
            $userid = $payload['sub'];
            // If request specified a G Suite domain:
            //$domain = $payload['hd'];
        } else {
            // Invalid ID token
        }
        var_dump($data);
        return new OAuth();
    }
}