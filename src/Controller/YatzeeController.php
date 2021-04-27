<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Dice\DiceHand;
use App\Dice\GraphicDice;
use App\Yatzee\ScoreChart;
use App\Yatzee\YatzeeLogic;

class YatzeeController extends AbstractController
{
    private YatzeeLogic $logic;
    private ScoreChart $scoreChart;
    private $session;
    private $request;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->request = Request::createFromGlobals();
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
    public function play(): Response
    {
        $this->initialise();
        $sessionChart = $this->session->get('chart');

        if (!$this->session->has('rolledValues')) {
            $this->logic->rollHand();
            $rolledDiceValues = $this->logic->getRolledDiceValues();
            $this->session->set('rolledValues', $rolledDiceValues);
        }

        $data = [
            "header" => "Yatzee",
            "chartArray" => $sessionChart,
            "n" => 1,
        ];
        return $this->render('play-yatzee.html.twig', $data);
    }

    /**
     * @Route("/yatzee/re-roll", methods={"POST"})
     */
    public function reroll(): Response
    {

        $this->initialise();

        if ($this->session->get('rollsLeft') > 0) {
            $this->rerollSelectedDice();
            $rollsLeft = $this->session->get('rollsLeft') - 1;
            $this->session->set('rollsLeft', $rollsLeft);
        }
        return $this->redirectToRoute('app_yatzee_play');
    }

    /**
     * @Route("/yatzee/score", methods={"POST"})
     */
    public function score(): Response
    {

        $this->initialise();
        $rolledValues = $this->session->get('rolledValues');

        $combos =  $this->logic->scorableCombos($rolledValues);
        $possibleScores = $this->logic->comboTotal($combos);
        $this->session->set('possibleScores', $possibleScores);
        return $this->redirectToRoute('app_yatzee_play');
    }

    /**
     * @Route("/yatzee/record-score", methods={"POST"})
     */
    public function recordScore(): Response
    {
        $this->initialise();
        $selectedScore = $this->request->request->get('selectedScore');

        if (null !== $selectedScore) {
            $key = $selectedScore;
            $possibleScores = $this->session->get('possibleScores');
            $this->logic->setScore($key, $possibleScores[$key]);
            $this->session->set('chart', $this->logic->getScores());
        }

        $this->session->remove('possibleScores');
        $this->session->remove('rolledValues');
        $this->session->set('rollsLeft', 2);

        return $this->redirectToRoute('app_yatzee_play');
    }

    /**
     * @Route("/yatzee/game-over", methods={"GET"})
     */
    public function gameOver(): Response
    {
        $this->scoreChart = new ScoreChart();
        $this->uploadChart();

        $this->session->set('rollsLeft', 2);
        $this->session->remove('rolledValues');
        return $this->redirectToRoute('app_yatzee_play');
    }

    private function initialise()
    {
        if ($this->session->has('chart')) {
            $chartArray = $this->session->get('chart');
            $this->scoreChart = new ScoreChart($chartArray);
            $this->logic = new YatzeeLogic($this->scoreChart->getScoreChart());
            return;
        }

        $newChart = new ScoreChart();
        $this->scoreChart = $newChart;
        $this->logic = new YatzeeLogic($this->scoreChart->getScoreChart());
    }


    private function rerollSelectedDice()
    {
        $originalRolls = $this->session->get('rolledValues');

        if (null == $this->request->request->get('selectedDice')) {
            return;
        }
        $selectedDice = $this->request->request->get('selectedDice');
        if (isset($selectedDice)) {
            $newDiceQty = count($selectedDice);
            $newDiceValues = $this->logic->reRoll($newDiceQty);
            $ior = 0;

            foreach ($selectedDice as $selected) {
                $originalRolls[$selected - 1] = $newDiceValues[$ior];
                $ior++;
            }
        }

        $this->session->set('rolledValues', $originalRolls);
    }


    private function uploadChart()
    {
        $this->session->set('chart', $this->scoreChart->getScoreChart());
    }
}
