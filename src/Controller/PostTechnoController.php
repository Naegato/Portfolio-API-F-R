<?php

namespace App\Controller;

use App\Entity\Techno;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostTechnoController extends AbstractController
{

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, EntityManagerInterface $manager)
    {
        try {
            $name = $request->get('name');
            $time = (int)$request->get('time');
            $image = $request->files->get('image');

            if (empty($name)) {
                throw new \Exception("The property 'name' must be an string not null");
            }

            if (empty($time)) {
                throw new \Exception("The property 'time' must be an integer not null");
            }

            if (empty($image)) {
                throw new \Exception("The property 'image' must be a file");
            }

            $techno = new Techno();
            $techno
                ->setFile($image)
                ->setName($name)
                ->setTime($time)
            ;

            $manager->persist($techno);
            $manager->flush();
            return new Response(null,201);

        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()),500);
        }
    }
}