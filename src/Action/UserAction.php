<?php
declare(strict_types=1);

namespace App\Action;

use App\Entity\User;
use App\Security\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;

class UserAction
{
    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route(
     *     name="api_register_user",
     *     path="/users/register",
     *     defaults={"_api_resource_class"=User::class, "_api_collection_operation_name"="special"}
     * )
     * @Method("POST")
     */
    public function __invoke(User $data): User
    {
        return $this->userManager->registerUser($data);
    }
}