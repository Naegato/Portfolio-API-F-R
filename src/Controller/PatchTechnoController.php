<?php

namespace App\Controller;

use App\Entity\Techno;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PatchTechnoController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $manager)
    {
        /**
         * @var Techno $object
         */
        $object = $request->attributes->get('data');

        $data = $request->request->all();
        $file = $request->files->all();

        foreach (array_merge($data,$file) as $key => $value) {
            $object->set($key,$value);
        }

        $manager->persist($object);
        $manager->flush();

        return $this->json($object, 200);
    }
}