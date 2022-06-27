<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\MeController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: UserRepository::class),
    ApiResource(
        description: 'User endpoint',
        collectionOperations: [
            'me' => [
                'pagination_enabled' => false,
                'path' => '/me',
                'method' => 'get',
                'controller' => MeController::class,
                'read' => false,
            ],
            'get' => [
                'normalization_context' => ['groups' => ['read:Users']],
            ],
            'post' => [
                'normalization_context' => ['groups' => ['auth:User']],
            ],
        ],
        itemOperations: [
            'get' => [
                'normalization_context' => ['groups' => 'read:User', 'read:Users']
            ],
            'put' => [
                'denormalization_context' => ['groups' => 'put:User']
            ],
            'delete',
        ],
        attributes: [
            'paginationItemsPerPage' => 15,
            'security' => 'is_granted("ROLE_USER")',
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ]
        ],
        paginationClientItemsPerPage: true,
    ),
    ApiFilter(
        SearchFilter::class, properties: ['id' => 'exact', 'firstName' => 'partial', 'lastName' => 'partial']
    )
]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column(type: 'integer')
    ]
    private $id;

    #[
        ORM\Column(type: 'string', length: 180, unique: true),
        Groups(['read:Users', 'auth:User']),
        Assert\Uuid
    ]
    private $uuid;

    #[
        ORM\Column(type: 'json'),
        Groups(['read:Users'])
    ]
    private $roles = [];

    #[
        ORM\Column(type: 'string'),
        Groups(['auth:User']),
    ]
    private $password;

    #[
        ORM\Column(type: 'string', length: 50),
        Groups(['read:Users']),
        Assert\Length(min: 2)
    ]
    private $firstName;

    #[
        ORM\Column(type: 'string', length: 100),
        Groups(['read:Users']),
        Assert\Length(min: 2)
    ]
    private $lastName;

    #[
        ORM\Column(type: 'string', length: 150, nullable: true),
        Groups(['read:User'])
    ]
    private $address;

    #[
        ORM\Column(type: 'string', length: 5, nullable: true),
        Groups(['read:User'])
    ]
    private $zipCode;

    #[
        ORM\Column(type: 'string', length: 50, nullable: true),
        Groups(['read:User'])
    ]
    private $country;

    #[
        ORM\Column(type: 'string', length: 10, nullable: true),
        Groups(['read:User'])
    ]
    private $phoneNumber;

    #[
        ORM\Column(type: 'date', nullable: true),
        Groups(['read:User'])
    ]
    private $birthdate;

    #[
        ORM\Column(type: 'datetime_immutable'),
        Groups(['read:User'])
    ]
    private $createdAt;

    #[
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Groups(['read:User'])
    ]
    private $updatedAt;

    #[
        ORM\Column(type: 'datetime_immutable', nullable: true),
        Groups(['read:User'])
    ]
    private $deletedAt;

    #[
        ORM\ManyToMany(targetEntity: Newsletter::class, inversedBy: 'users'),
        Groups(['read:User'])
    ]
    private $newsletters;

    public function __construct()
    {
        $this->newsletters = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->uuid;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public static function createFromPayload($id, array $payload): UserInterface
    {
        return (new User())->setId($id);
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, Newsletter>
     */
    public function getNewsletters(): Collection
    {
        return $this->newsletters;
    }

    public function addNewsletter(Newsletter $newsletter): self
    {
        if (!$this->newsletters->contains($newsletter)) {
            $this->newsletters[] = $newsletter;
        }

        return $this;
    }

    public function removeNewsletter(Newsletter $newsletter): self
    {
        $this->newsletters->removeElement($newsletter);

        return $this;
    }
}
