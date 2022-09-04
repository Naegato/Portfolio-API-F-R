<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\DeleteTechnoController;
use App\Controller\PatchTechnoController;
use App\Controller\PutTechnoController;
use App\Controller\PostTechnoController;
use App\Repository\TechnoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable()
 */
#[ORM\Entity(repositoryClass: TechnoRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'pagination_enabled' => false,
            'normalization_context' => ['groups' => ['read:techno:collection']],
            'openapi_context' => [
                'summary' => 'Return a collection of Technology',
                'description' => 'Return a collection of Technology',
                'responses' => [
                    '200' => [
                        'description' => 'Ok',
                        'content' => [
                            'application/json' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => [
                                            'type' => 'string',
                                        ],
                                        'time' => [
                                            'type' => 'integer',
                                        ],
                                        'image' => [
                                            'type' => 'string',
                                        ],
                                        'base64' => [
                                            'type' => 'string',
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'post'=> [
            'method' => 'post',
            'path' => '/technos',
            'deserialize' => false,
            'denormalization_context' => ['groups' => ['write:techno']],
            'security' => 'is_granted(\'ROLE_USER\')',
            'openapi_context' => [
                'security' => [['JWT' => []]],
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => [
                                        'type' => 'string',
                                    ],
                                    'time' => [
                                        'integer',
                                    ],
                                    'image' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Resource created',
                    ]
                ]
            ],
            'controller' => PostTechnoController::class,
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:techno:item']]
        ],
        'patch' => [
            'deserialize' => false,
            'method' => 'post',
            'path' => '/technos/{id}',
            'security' => 'is_granted(\'ROLE_USER\')',
            'openapi_context' => [
                'security' => [['JWT' => []]],
            ],
            'controller' => PatchTechnoController::class,
        ],
        'delete' => [
            'security' => 'is_granted(\'ROLE_USER\')',
            'openapi_context' => [
                'security' => [['JWT' => []]],
            ],
        ],
    ]
)]
class Techno
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['read:techno:collection','read:techno:item','write:techno'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['read:techno:collection','read:techno:item'])]
    #[ORM\Column(length: 255,nullable: true)]
    private ?string $image = null;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="post_techno",fileNameProperty="image")
     */
    #[Groups(['write:techno'])]
    private $file;

    #[Groups(['read:techno:item'])]
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'technos')]
    private Collection $projects;

    #[Groups(['read:techno:collection','read:techno:item','write:techno'])]
    #[ORM\Column]
    private ?int $time = null;

    #[Groups(['read:techno:collection','read:techno:item'])]
    private string $base64;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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

    public function setImage(string|null $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addTechno($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            $project->removeTechno($this);
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     */
    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getBase64(): string
    {
        return $this->base64;
    }

    /**
     * @param string $base64
     */
    public function setBase64(string $base64): self
    {
        $this->base64 = $base64;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function set(string $key, mixed $value) {
        if (property_exists($this, $key)) {
            if (get_debug_type($this->{$key}) == 'int') {
                $this->{$key} = (int)$value;
            } else {
                $this->{$key} = $value;
            }
        }
    }
}
