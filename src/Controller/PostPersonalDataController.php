<?php

namespace App\Controller;

use App\Repository\PersonalDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostPersonalDataController extends AbstractController
{
    public function __construct(private $rootpath,private PersonalDataRepository $repository)
    {
    }

    public function __invoke(Request $request)
    {
        try {
            $data = array_merge($request->request->all(),$request->files->all());
            unset($data['photo']);
            $personalData = $this->repository->getData();

            foreach ($data as $key => $value) {
                $personalData->set($key,$value);
            }

            file_put_contents($this->rootpath.'/src/Data/personal-data.json',json_encode($personalData->toArray()));

            return new Response(null,201);
        } catch (\Exception $e) {
            return new Response($e->getMessage(),500);
        }
    }
}