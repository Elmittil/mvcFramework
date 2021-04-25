<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Dice\DiceHand;
use App\Dice\GraphicDice;
use App\Yatzee\ScoreChart;
use App\Yatzee\YatzeeLogic;

use function Mos\Functions\{
    renderView,
    url
};

class YatzeeController extends AbstractController
{
    private YatzeeLogic $logic;
    private ScoreChart $scoreChart;
    private array $currentSession;
    private $session;
    // private $request;
    

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        // $this->request = Request::createFromGlobals();
    }

    /**
     * @Route("/yatzee")
     */
    public function intro(): Response
    {
        $this->initialise();
        $this->session->set('rollsLeft', 2);
        $this->uploadChart();

        $data = [
            "header" => "Yatzee",
        ];

        return $this->render('yatzee.html.twig', $data); 
    }
 
    /**
     * @Route("/yatzee/play", methods={"GET", "POST"})
     */
    public function play(): Response {
        $this->initialise();
        $sessionChart = $this->session->get('chart');

        if (!array_key_exists('rolledValues', $this->currentSession)) {
            $this->logic->rollHand();
            $rolledDiceValues = $this->logic->getRolledDiceValues();
            $_SESSION['rolledValues'] = $rolledDiceValues;
        }

        $data = [
            "header" => "Yatzee",
            "chartArray" => $sessionChart,
        ];
        return $this->render('play-yatzee.html.twig', $data); 
    }

    /**
     * @Route("/yatzee/re-roll", methods={"POST"})
     */
    public function reroll(): Response {

        $this->initialise();

        if ($_SESSION['rollsLeft'] > 0) {
            $this->rerollSelectedDice();
            $_SESSION['rollsLeft'] = $_SESSION['rollsLeft'] - 1;
        }
        return $this->redirectToRoute('app_yatzee_play'); 
    }

    /**
     * @Route("/yatzee/score", methods={"POST"})
     */
    public function score(): Response {

        $this->initialise();

        $combos =  $this->logic->scorableCombos($_SESSION['rolledValues']);
        $possibleScores = $this->logic->comboTotal($combos);
        $_SESSION['possibleScores'] = $possibleScores;
        return $this->redirectToRoute('app_yatzee_play'); 
    }

    /**
     * @Route("/yatzee/record-score", methods={"POST"})
     */
    public function recordScore(): Response {
        $this->initialise();

        if (isset($_POST['selectedScore'])) {
            $key = $_POST['selectedScore'];
            $this->logic->setScore($key, $_SESSION['possibleScores'][$key]);
            $_SESSION['chart'] = $this->logic->getScores();
        }
        unset($_SESSION['possibleScores']);
        unset($_SESSION['rolledValues']);
        $_SESSION['rollsLeft'] = 2;

        return $this->redirectToRoute('app_yatzee_play'); 
    }

    /**
     * @Route("/yatzee/game-over", methods={"GET"})
     */
    public function gameOver(): Response 
    {
        $this->scoreChart = new ScoreChart();
        $this->uploadChart();

        $_SESSION['rollsLeft'] = 2;
        return $this->redirectToRoute('app_yatzee_play'); 
    }

    private function initialise()
    {
        if ($this->session->has('chart')) {
            $chartArray = $this->session->get('chart');
            $this->scoreChart = new ScoreChart($chartArray);
        } else {
            $newChart = new ScoreChart();
            $this->scoreChart = $newChart;
        }

        $this->logic = new YatzeeLogic($this->scoreChart->getScoreChart());
    }


    private function rerollSelectedDice()
    {
        $originalRolls = $_SESSION['rolledValues'];
        if (!isset($_POST['selectedDice'])) {
            return;
        }
        $selectedDice = $_POST['selectedDice'];

        $newDiceQty = count($selectedDice);
        $newDiceValues = $this->logic->reRoll($newDiceQty);
        $ior = 0;

        foreach ($selectedDice as $selected) {
            $originalRolls[$selected - 1] = $newDiceValues[$ior];
            $ior++;
        }
        $_SESSION['rolledValues'] = $originalRolls;
    }


    private function uploadChart()
    {
        $_SESSION['chart'] = $this->scoreChart->getScoreChart();
    }
}
