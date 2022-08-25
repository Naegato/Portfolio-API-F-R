<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:project:collection']]
        ],
        'post'=> [
            'security' => 'is_granted(\'ROLE_USER\')',
            'openapi_context' => [
                'security' => [['JWT' => []]],
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:project:item']]
        ],
        'patch' => [
            'security' => 'is_granted(\'ROLE_USER\')',
            'openapi_context' => [
                'security' => [['JWT' => []]],
            ],
        ],
        'delete' => [
            'security' => 'is_granted(\'ROLE_USER\')',
            'openapi_context' => [
                'security' => [['JWT' => []]],
            ],
        ],
    ]
)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:project:collection'])]
    private ?int $id = null;

    #[Groups(['read:project:collection','read:project:item'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['read:project:collection','read:project:item'])]
    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[Groups(['read:project:item'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Groups(['read:project:item'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $link = null;

    #[Groups(['read:project:item'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[Groups(['read:project:collection','read:project:item'])]
    #[ORM\ManyToMany(targetEntity: Techno::class, inversedBy: 'projects')]
    private Collection $technos;

    public function __construct()
    {
        $this->technos = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, Techno>
     */
    public function getTechnos(): Collection
    {
        return $this->technos;
    }

    public function addTechno(Techno $techno): self
    {
        if (!$this->technos->contains($techno)) {
            $this->technos->add($techno);
        }

        return $this;
    }

    public function removeTechno(Techno $techno): self
    {
        $this->technos->removeElement($techno);

        return $this;
    }
}
