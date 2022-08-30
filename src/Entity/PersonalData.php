<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\PostPersonalDataController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\OrignameNamer;
use Vich\UploaderBundle\Naming\SlugNamer;
use Vich\UploaderBundle\Util\Transliterator;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'path' => 'personal_data'
        ],
        'post' => [
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
        if (property_exists($this, $key)) {
            if ($key === 'file') {
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

    public function toArray() {
        return ["name" => $this->getName(),"description" => $this->getDescription(),"photo" => $this->photo];
    }
}