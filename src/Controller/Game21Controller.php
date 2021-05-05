<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Dice\DiceHand;
use App\Entity\Score;

require_once __DIR__ . "/../../bin/bootstrap.php";

class Game21Controller extends AbstractController
{
    private $session;
    private $request;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->request = Request::createFromGlobals();
    }

    /**
     * @Route("/game21")
     */
    public function game21start(): Response
    {

        $data = [
            "header" => "Game21",
            "message" => "Let's play again",

        ];

        $this->session->set('rollPlayer', 0);
        $this->session->set('rollComputer', 0);
        $this->session->set('score', array());
        $this->session->set('totalPlayer', 0);
        $this->session->set('totalComputer', 0);
        $this->session->set('message', "");


        return $this->render('game21.html.twig', $data);
    }

    /**
     * @Route("/game21/play", methods={"GET", "POST"})
     */
    public function game21play(): Response
    {
        $playerName = $this->session->get('playerName');
        $diceQty =  $this->session->get('diceQty');

        if (array_key_exists('button1', $_POST)) {
            $this->buttonRoll((int)$diceQty);
        } else if (array_key_exists('button2', $_POST)) {
            $this->buttonPass((int)$diceQty);
        }

        $data = [
            "header" => "GAME 21",
            'player' => $playerName,
        ];

        return $this->render('play.html.twig', $data);
    }

    /**
     * @Route("/game21/set-hand",  methods={"POST"})
     */
    public function game21setHand(): Response
    {
        $playername = $this->request->request->get('playername');
        if (null == $this->request->request->get('diceQty')) {
            $diceQty = 1;
            $this->get('session')->set('diceQty', $diceQty);
            $this->get('session')->set('playerName', $playername);
            return $this->redirectToRoute('app_game21_game21play');
        }

        $diceQty = $this->request->request->get('diceQty');
        $this->get('session')->set('diceQty', $diceQty);
        $this->get('session')->set('playerName', $playername);
        return $this->redirectToRoute('app_game21_game21play');
    }

    /**
     * @Route("/game21/reset",  methods={"GET"})
     */
    public function game21reset(): Response
    {
        $this->resetGame();
        return $this->redirectToRoute('app_game21_game21play');
    }

    /**
     * @Route("/game21/save-score",  methods={"GET"})
     */
    public function game21SaveScore(): Response
    {
        $playerName = $this->session->get('playerName');

        $totalScores = $this->session->get('score');
        $playerScore = $this->countScores($totalScores);

        $score = new Score();
        if ($playerName) {
            $score->setPlayerName($playerName);
        }
        if (!$playerName) {
            $score->setPlayerName("anonymous");
        }
        $score->setScore($playerScore);
        $score->setGame("game21");

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($score);
        $entityManager->flush();

        $this->resetGame();
        return $this->redirectToRoute('app_game21_game21start');
    }

    private function buttonRoll(int $diceQty): void
    {
        $oldTotalPlayer = $this->session->get('totalPlayer');
        $hand = $this->setupAndRoll($diceQty);

        $newTotalPLayer = $oldTotalPlayer + $hand;

        $this->session->set('rollPlayer', $hand);
        $this->session->set('totalPlayer', $newTotalPLayer);

        if ($this->checkIfOver21("COMPUTER", $newTotalPLayer)) {
            return;
        }

        $oldTotalComputer = $this->session->get('totalComputer');
        $newTotalComputer = $oldTotalComputer;

        if ($this->shouldComputerRoll($newTotalPLayer, $oldTotalComputer)) {
            $hand = $this->setupAndRoll($diceQty);
            $this->session->set('rollComputer', $hand);
            $newTotalComputer = $oldTotalComputer + $hand;
            $this->session->set('totalComputer', $newTotalComputer);
        }

        if ($this->checkIfOver21("YOU", $newTotalComputer)) {
            return;
        }

        if ($this->checkIf21($newTotalComputer)) {
            return;
        }
    }

    private function buttonPass(int $diceQty): void
    {
        $totalPlayer = $this->session->get('totalPlayer');
        $totalComputer = $this->session->get('totalComputer');

        $computerHands = array();

        while ($totalComputer <= $totalPlayer) {
            $hand = $this->setupAndRoll($diceQty);
            $totalComputer = $totalComputer + $hand;
            array_push($computerHands, $hand);
        }
        $this->session->set('rollComputer', implode('+', $computerHands));
        $this->session->set('totalComputer', $totalComputer);

        if ($totalComputer <= 21) {
            $message = "COMPUTER WON!!!";
            $this->session->set('message', $message);

            $newScore = $this->session->get('score');
            array_push($newScore, ["", "x"]);
            $this->session->set('score', $newScore);
            return;
        }

        $message = "YOU WON!!!";
        $newScore = $this->session->get('score');
        $this->session->set('message', $message);
        array_push($newScore, ["x", ""]);
        $this->session->set('score', $newScore);
        return;
    }

    private function resetGame()
    {
        $this->session->set('rollPlayer', 0);
        $this->session->set('rollComputer', 0);
        // $this->session->set('score', array());
        $this->session->set('totalPlayer', 0);
        $this->session->set('totalComputer', 0);
        $this->session->set('message', "");
    }

    private function setupAndRoll(int $diceQty): int
    {
        $hand = new DiceHand($diceQty, "regular");
        $hand->roll($diceQty);
        $rolled =  $hand->getRollSum();
        return $rolled;
    }

    private function shouldComputerRoll(int $playerScore, int $computerScore): bool
    {
        if ($computerScore < 21 && $computerScore < $playerScore) {
            return true;
        }
        return false;
    }

    private function checkIfOver21(string $who, int $total): bool
    {
        if ($total > 21) {
            $message = $who . " WON!!!";
            $this->session->set('message', $message);

            if ($who === "COMPUTER") {
                $newScore = $this->session->get('score');
                array_push($newScore, ["", "x"]);
                $this->session->set('score', $newScore);
            }
            if ($who === "YOU") {
                $newScore = $this->session->get('score');
                array_push($newScore, ["x", ""]);
                $this->session->set('score', $newScore);
            }
            return true;
        }
        return false;
    }

    private function checkIf21(int $total): bool
    {
        if ($total == 21) {
            $message = "COMPUTER WON!!!";
            $this->session->set('message', $message);

            $newScore = $this->session->get('score');
            array_push($newScore, ["", "x"]);
            $this->session->set('score', $newScore);
            return true;
        }
        return false;
    }

    private function countScores(array $scores): int
    {
        $playersScores = array();
        foreach ($scores as $score) {
                array_push($playersScores, $score[0]);
        }
        $counts = array_count_values($playersScores);
        if (array_key_exists("x", $counts)) {
            return $counts["x"];
        }
        return 0;
    }
}
