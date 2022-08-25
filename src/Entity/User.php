<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\DeleteUserController;
use App\Controller\MeController;
use App\Controller\MePutController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:user:collection']],
            'security' => 'is_granted("ROLE_USER")'
        ],
        'post' =>[
            'denormalization_context' => ['groups' => ['write:user']],
            'normalization_context' => ['groups' => ['read:user:collection']],
            'path' => '/users/register'
        ]
    ],
    itemOperations: [
        'get' => [
            'controller' => NotFoundAction::class,
            'read' => false,
            'output' => false,
            'openapi_context' => [
                'summary' => 'hidden'
            ]
        ],
        'me' => [
            'normalization_context' => ['groups' => ['read:user:item']],
            'method' => 'get',
            'path'=> '/me',
            'controller' => MeController::class,
            'read' => false,
            'security' => 'is_granted("ROLE_USER")'
        ],
        'delete' => [
            'path' => '/users',
            'controller' => DeleteUserController::class,
            'read' => false,
            'write' => false,
            'security' => 'is_granted("ROLE_USER")'
        ],
        'put'=>[
            'path'=> '/users',
            'controller' => MePutController::class,
            'normalization_context' => ['groups' => ['read:user:update']],
            'denormalization_context' => ['groups' => ['write:user:update']],
            'security' => 'is_granted("ROLE_USER")',
            'read' => false,
            'write' => false
        ]
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['read:user:item', 'write:user', 'read:user:update', 'write:user:update'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['read:user:item'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['write:user'])]
    #[Length(min: 60)]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['read:user:collection', 'read:user:item', 'write:user', 'read:user:update', 'write:user:update'])]
    private ?string $username = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Liste::class, orphanRemoval: true)]
    #[Groups(['read:user:item'])]
    private Collection $listes;

    #[ORM\ManyToMany(targetEntity: Partage::class, mappedBy: 'user', orphanRemoval: true)]
    #[Groups(['read:user:item'])]
    private Collection $partages;

    public function __construct()
    {
        $this->listes = new ArrayCollection();
        $this->partages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Liste>
     */
    public function getListes(): Collection
    {
        return $this->listes;
    }

    public function setListes(Collection $listes): self
    {
        $this->listes = $listes;
        return $this;
    }

    public function addListe(Liste $liste): self
    {
        if (!$this->listes->contains($liste)) {
            $this->listes->add($liste);
            $liste->setUser($this);
        }

        return $this;
    }

    public function removeListe(Liste $liste): self
    {
        if ($this->listes->removeElement($liste)) {
            // set the owning side to null (unless already changed)
            if ($liste->getUser() === $this) {
                $liste->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Partage>
     */
    public function getPartages(): Collection
    {
        return $this->partages;
    }

    public function setPartages(Collection $partages): self
    {
        $this->partages = $partages;
        return $this;
    }

    public function addPartage(Partage $partage): self
    {
        if (!$this->partages->contains($partage)) {
            $this->partages->add($partage);
            $partage->addUser($this);
        }

        return $this;
    }

    public function removePartage(Partage $partage): self
    {
        if ($this->partages->removeElement($partage)) {
            $partage->removeUser($this);
        }

        return $this;
    }

    public static function createFromPayload($username, array $payload)
    {
    }
}
