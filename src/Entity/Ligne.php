<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\DeleteLigneController;
use App\Controller\UserTakenPartageController;
use App\Repository\LigneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LigneRepository::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get' => [
            'controller' => NotFoundAction::class,
            'read' => false,
            'output' => false,
            'openapi_context' => [
                'summary' => 'hidden'
            ]
        ],
        'put' => [
            'normalization_context' => ['groups' => ['read:ligne:user']],
            'denormalization_context' => ['groups' => ['write:ligne:user']]
        ],
        'put_partage' => [
            'normalization_context' => ['groups' => ['read:ligne:user_taken']],
            'controller' => UserTakenPartageController::class,
            'method' => 'put',
            'path'=> '/lignes/{id}/user_taken',
            'write' => false
        ],
        'delete' => [
            'controller' => DeleteLigneController::class,
            'write' => false
        ]
    ]
)]
class Ligne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:liste:user', 'read:liste:partage', 'write:liste:add_ligne', 'read:ligne:user'])]
    private ?int $position = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:liste:user', 'read:liste:partage', 'write:liste:add_ligne', 'read:ligne:user', 'write:ligne:user'])]
    private ?string $contenu = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:liste:user', 'read:liste:partage', 'write:liste:add_ligne', 'read:ligne:user', 'write:ligne:user'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:liste:user', 'read:liste:partage', 'write:liste:add_ligne', 'read:ligne:user', 'write:ligne:user'])]
    private ?string $lien = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Liste $liste = null;

    #[ORM\ManyToOne]
    #[Groups(['read:liste:partage', 'read:ligne:user_taken'])]
    private ?User $user_taken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getListe(): ?Liste
    {
        return $this->liste;
    }

    public function setListe(?Liste $liste): self
    {
        $this->liste = $liste;

        return $this;
    }

    public function getUserTaken(): ?User
    {
        return $this->user_taken;
    }

    public function setUserTaken(?User $user_taken): self
    {
        $this->user_taken = $user_taken;

        return $this;
    }
}
