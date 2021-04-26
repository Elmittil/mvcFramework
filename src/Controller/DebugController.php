<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function Mos\Functions\{
    url,
    getBaseUrl,
    getCurrentUrl,
    getRoutePath
};

// require(INSTALL_PATH . '/src/functions.php');

class DebugController extends AbstractController
{
    /**
     * @Route("/debug")
     */
    public function debug(): Response
    {
        $baseUrl = getBaseUrl();
        $currentUrl = getCurrentUrl();
        $routePath = getRoutePath();
        // $server = $_SERVER;
        return $this->render(
            'debug.html.twig',
            // body data
            [   "header" => "Debug page",
                "baseUrl" => $baseUrl,
                "currentUrl" => $currentUrl,
                "routePath" => $routePath,
                // "server" => $_SERVER,
            ]
        );
    }
}
