<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class OAuth
 * @package App\Entity
 *
 * @ApiResource(collectionOperations={"post"={"method"="POST"},"login_oauth"={"route_name"="api_login_oauth"}}, itemOperations={})
 */
class OAuth
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $provider;
    private $authDomain;
    private $authMethod;
    private $displayName;
    /**
     * @var string
     * @Assert\Email()
     */
    private $email;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=25)
     */
    private $idToken;
    private $profilePicture;

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $provider
     * @return OAuth
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthDomain()
    {
        return $this->authDomain;
    }

    /**
     * @param mixed $authDomain
     * @return OAuth
     */
    public function setAuthDomain($authDomain)
    {
        $this->authDomain = $authDomain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthMethod()
    {
        return $this->authMethod;
    }

    /**
     * @param mixed $authMethod
     * @return OAuth
     */
    public function setAuthMethod($authMethod)
    {
        $this->authMethod = $authMethod;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param mixed $displayName
     * @return OAuth
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return OAuth
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdToken()
    {
        return $this->idToken;
    }

    /**
     * @param mixed $idToken
     * @return OAuth
     */
    public function setIdToken($idToken)
    {
        $this->idToken = $idToken;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param mixed $profilePicture
     * @return OAuth
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

}
