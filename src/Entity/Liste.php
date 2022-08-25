<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\AddLigneController;
use App\Controller\RemoveListeController;
use App\Repository\ListeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ListeRepository::class)]
#[ApiResource(
    collectionOperations: [
        'post' =>[
            'denormalization_context' => [
                'groups' => ['write:liste:new']
            ]
        ]
    ],
    itemOperations: [
        'get' =>[
            'normalization_context' => ['groups' => ['read:liste:user']]
        ],
        'get_partage' => [
            'normalization_context' => ['groups' => ['read:liste:partage']],
            'method' => 'get',
            'path'=> '/listes/{id}/partage',
        ],
        'put' =>[
            'denormalization_context' => [
                'groups' => ['write:liste:name']
            ],
            'normalization_context' => ['groups' => ['write:liste:name']]
        ],
        'post_ligne' => [
            'controller' => AddLigneController::class,
            'method' => 'post',
            'normalization_context' => ['groups' => ['write:liste:add_ligne']],
            'path' => '/listes/{id}/ligne',
            'write' => false
        ],
        'delete' => [
            'normalization_context' => ['groups' => ['delete:liste']],
            'controller' => RemoveListeController::class,
            'write' => false
        ]
    ],
    security: 'is_granted("ROLE_USER")'
)]
class Liste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:user:item', 'read:liste:user','write:liste:new', 'write:liste:name', 'read:liste:partage'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'listes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Partage::class, mappedBy: 'liste', orphanRemoval: true)]
    #[Groups(['read:liste:user'])]
    private Collection $partages;

    #[ORM\OneToMany(mappedBy: 'liste', targetEntity: Ligne::class, orphanRemoval: true, cascade: ["persist"])]
    #[Groups(['read:liste:user', 'read:liste:partage', 'write:liste:add_ligne'])]
    private Collection $lignes;

    public function __construct()
    {
        $this->partages = new ArrayCollection();
        $this->lignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Partage>
     */
    public function getPartages(): Collection
    {
        return $this->partages;
    }

    public function addPartage(Partage $partage): self
    {
        if (!$this->partages->contains($partage)) {
            $this->partages->add($partage);
            $partage->addListe($this);
        }

        return $this;
    }

    public function removePartage(Partage $partage): self
    {
        if ($this->partages->removeElement($partage)) {
            $partage->removeListe($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Ligne>
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(Ligne $ligne): self
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setListe($this);
        }

        return $this;
    }

    public function removeLigne(Ligne $ligne): self
    {
        if ($this->lignes->removeElement($ligne)) {
            // set the owning side to null (unless already changed)
            if ($ligne->getListe() === $this) {
                $ligne->setListe(null);
            }
        }

        return $this;
    }

    public function getMaxLignePosition(): int
    {
        $maxPosition = -1;
        foreach ($this->getLignes() as $ligne){
            if($ligne->getPosition() > $maxPosition){
                $maxPosition = $ligne->getPosition();
            }
        }

        return $maxPosition;
    }
}
