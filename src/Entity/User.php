<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    private $locale = 'en';
    /**
     * @ORM\Column(type="string", length=155)
     * @var string
     */
    private $name = '';
    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $profilePicture = '';
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
    /**
     * @var ArrayCollection|Token[]
     * @ORM\OneToMany(targetEntity="Token", mappedBy="user", cascade={"persist", "remove"})
     */
    private $tokens;

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
        $this->tokens = new ArrayCollection();
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

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return User
     */
    public function setLocale(string $locale): User
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param ArrayCollection $tokens
     *
     * @return User
     */
    public function setTokens(ArrayCollection $tokens): User
    {
        $this->tokens = $tokens;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTokens(): ArrayCollection
    {
        return $this->tokens;
    }

    /**
     * Add tokens
     *
     * @param Token $token
     *
     * @return User
     */
    public function addToken(Token $token): User
    {
        $this->tokens[] = $token;
        $token->setUser($this);

        return $this;
    }

    /**
     * Remove tokens
     *
     * @param Token $token
     * @return User
     */
    public function removeToken(Token $token): User
    {
        $this->tokens->removeElement($token);

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getProfilePicture(): string
    {
        return $this->profilePicture;
    }

    /**
     * @param string $profilePicture
     * @return User
     */
    public function setProfilePicture(string $profilePicture): User
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }
}
