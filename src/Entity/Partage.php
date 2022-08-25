<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\PostPartageController;
use App\Repository\PartageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PartageRepository::class)]
#[ApiResource(
    collectionOperations: [
        'post' => [
            'controller' => PostPartageController::class,
            'write' => false
        ]
    ],
    itemOperations: [
        'delete',
        'get' => [
            'controller' => NotFoundAction::class,
            'openapi_context' => [
                'summary' => 'hidden'
            ],
            'read' => false,
            'output' => false
        ]

    ],
    security: 'is_granted("ROLE_USER")'
)]
class Partage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'partages')]
    #[Groups(['read:liste:user'])]
    private Collection $user;

    #[ORM\ManyToMany(targetEntity: Liste::class, inversedBy: 'partages')]
    #[Groups(['read:user:item'])]
    private Collection $liste;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->liste = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Liste>
     */
    public function getListe(): Collection
    {
        return $this->liste;
    }

    public function addListe(Liste $liste): self
    {
        if (!$this->liste->contains($liste)) {
            $this->liste->add($liste);
        }

        return $this;
    }

    public function removeListe(Liste $liste): self
    {
        $this->liste->removeElement($liste);

        return $this;
    }
}
