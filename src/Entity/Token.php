<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Token
 * @package App\Entity
 * @ApiResource(collectionOperations={}, itemOperations={"get"={"method"="GET"}})
 * @ORM\Entity()
 */
class Token
{
    /** @var  int
     *  @ORM\Column(type="integer")
     *  @ORM\Id
     */
    private $id;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tokens")
     * @ORM\JoinColumn(name="user", nullable=false)
     */
    private $user;
    /**
     * @var int
     * @ORM\Column(type="bigint")
     */
    private $userId;
    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $token;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $issued;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $expiration;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Token
     */
    public function setId(int $id): Token
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Token
     */
    public function setUser(User $user): Token
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return Token
     */
    public function setUserId(int $userId): Token
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return Token
     */
    public function setToken(string $token): Token
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return int
     */
    public function getIssued(): int
    {
        return $this->issued;
    }

    /**
     * @param int $issued
     * @return Token
     */
    public function setIssued(int $issued): Token
    {
        $this->issued = $issued;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiration(): int
    {
        return $this->expiration;
    }

    /**
     * @param int $expiration
     * @return Token
     */
    public function setExpiration(int $expiration): Token
    {
        $this->expiration = $expiration;
        return $this;
    }


}
