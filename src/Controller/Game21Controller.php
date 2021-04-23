<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function Mos\Functions\{
    renderView,
    url,
    resetGame
};

class Game21Controller extends AbstractController
{
    /**
     * @Route("/game21")
     */
    public function game21start(): Response
    {
        $data = [
            "header" => "Game21",
            "message" => "Let's play again",
        ];

        $playerRolls = 0;
        $computerRolls = 0;
        $_SESSION['roll'] = array($playerRolls, $computerRolls);
        $_SESSION['score'] = array();
        $_SESSION['total'] = array(0 , 0);
        $_SESSION['message'] = "";

        return $this->render('game21.html.twig', $data); 
    }
}
