<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function symfonyIndex(): Response
    {
        return $this->render(
            'migratedIndex.html.twig',
            [   "header" => "Symfony",
                "message" => "This page is a framework test",
            ]
        );
    }
}
