<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\PostPersonalDataController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\UnicodeString;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'path' => 'personal_data'
        ],
        'post' => [
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
                                    'description' => [
                                        'type' => 'string',
                                    ],
                                    'file' => [
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
                        'description' => 'Données personels mise a jour',
                        'content' => [],
                    ]
                ]
            ],
            'path' => 'personal_data',
            'controller' => PostPersonalDataController::class,
            'deserialize' => false,
        ]
    ],
    itemOperations: [],
)]
class PersonalData
{
    #[ApiProperty(
        identifier: true,
    )]
    private string $name;
    private string $description;
    private string $photo;
    /**
     * @var File|null
     * @Vich\UploadableField(mapping="post_personal",fileNameProperty="image")
     */
    private ?File $file;

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
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
    public function setFile(?File $file): void
    {
        $this->file = $file;
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
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function set(string $key, mixed $value) {
        if (!empty($value)) {
            if (property_exists($this, $key)) {
                if ($key === 'file' && $value instanceof File) {
                    $path = 'images/personal/';
                    $previousElement = scandir($path);
                    foreach ($previousElement as $item) {
                        if ($item != '.' && $item != '..') {
                            unlink($path.$item);
                        }
                    }
                    /**
                     * @var UploadedFile $value
                     */
                    $newName = (
                        uniqid().
                        '_'.
                        (
                            new UnicodeString(
                                substr(
                                    $value->getClientOriginalName(),
                                    0,
                                    strpos(
                                        $value->getClientOriginalName(),
                                        '.'
                                    ) - strlen($value->getClientOriginalName())
                                )
                            )
                        )->camel()->lower().
                        '.'.
                        $value->getClientOriginalExtension()
                    );

                    move_uploaded_file($value,$path.$newName);
                    $this->setPhoto($newName);
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }

    public function toArray() {
        return ["name" => $this->getName(),"description" => $this->getDescription(),"photo" => $this->photo];
    }
}