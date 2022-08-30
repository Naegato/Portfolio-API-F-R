<?php

namespace App\Repository;

use App\Entity\PersonalData;

class PersonalDataRepository
{
    public function __construct(private $rootpath)
    {
    }

    public function getData(): PersonalData {
        $jsonData = json_decode(file_get_contents($this->rootpath.'/src/Data/personal-data.json'));
        $personalData = new PersonalData();

        foreach ($jsonData as $key => $value) {
            $personalData->set($key,$value);
        }

        return $personalData;
    }
}