<?php

namespace App\Security;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserManager implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoder
     */
    private $encoder;

    public function __construct(UserPasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['registerUser', EventPriorities::POST_VALIDATE],
        ];
    }

    public function registerUser(GetResponseForControllerResultEvent $class) {
        if (!$this->supportsClass(get_class($class->getControllerResult()))) {
            return $class;
        }

        // Encrypt the password
        /** @var $user User */
        $user = $class->getControllerResult();
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        $class->setControllerResult($user);

        return $class;
    }

    private function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
