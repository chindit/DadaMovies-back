<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package App\Entity
 * @ApiResource(attributes={"normalization_context"={"groups"={"user"}}},
 *     collectionOperations={
 *     "register_user"={"route_name"="api_register_user"}
 * })
 * @ORM\Entity()
 */
class User implements UserInterface, EquatableInterface
{
    /** @var  int
     *  @ORM\Column(type="integer")
     *  @ORM\Id
     *  @ORM\GeneratedValue(strategy="AUTO")
     *  @ApiProperty(identifier=true)
     */
    public $id;
    /**
     *  @ORM\Column(type="string", length=100)
     *  @var string
     *  @Assert\NotBlank()
     *  @Assert\Length(min=5, max=100)
     *  @Assert\Email()
     */
    private $username = '';
    /**
     *  @ORM\Column(type="string", length=200)
     *  @var string
     *  @Assert\NotBlank()
     *  @Assert\Length(min=8)
     */
    private $password = '';
    /**
     * @ORM\Column(type="string", length=25)
     * @var string
     */
    private $salt = '';
    /**
     * @ORM\Column(type="array")
     * @var array
     */
    private $roles = [];
    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $notifications = false;
    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $premium = false;
    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    public function __construct(string $username = '', string $password = '', string $salt = '', array $roles = [])
    {
        if ($username) {
            $this->username = $username;
        }
        if ($password) {
            $this->password = $password;
        }
        if ($salt) {
            $this->salt = $salt;
        } else {
            $this->salt = bin2hex(openssl_random_pseudo_bytes(25));
        }
        if ($roles) {
            $this->roles = $roles;
        }
        $this->createdAt = new \DateTime();
    }

    /**
     * @Groups({"user"})
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): User
    {
        $this->roles[] = $role;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @Groups({"user"})
     */
    public function getNotifications(): bool
    {
        return $this->notifications;
    }

    public function setNotifications(bool $notifications): User
    {
        $this->notifications = $notifications;

        return $this;
    }

    /**
     * @Groups({"user"})
     */
    public function getPremium(): bool
    {
        return $this->premium;
    }

    public function setPremium(bool $premium): User
    {
        $this->premium = $premium;

        return $this;
    }

    /**
     * @Groups({"user"})
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @Groups({"user"})
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
