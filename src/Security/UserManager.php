<?php

namespace App\Security;


use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserManager
{
    /**
     * @var UserPasswordEncoder
     */
    private $encoder;

    public function __construct(UserPasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function registerUser(User $user) {
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        $user->setPremium(false);
        $user->addRole('ROLE_USER');

        return $user;
    }
}
