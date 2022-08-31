<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\PersonalData;
use App\Repository\PersonalDataRepository;

class PersonalDataDataProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{

    public function __construct(private $rootpath,private PersonalDataRepository $repository)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return PersonalData::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $personalData = $this->repository->getData();
        $personalData->setPhoto(base64_encode(file_get_contents($this->rootpath.'/public/images/personal/'.$personalData->getPhoto())));
        return [$personalData->toArray()];
    }
}