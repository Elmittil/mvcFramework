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
    public function migratedIndex(): Response
    {
        return $this->render('migratedIndex.html.twig',
            // body data
            [   "header" => "Index page",
                "message" => "Hello, this is the index page, rendered as a layout.",
            ]); 
    }
}
