<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ScoreRepository;
use App\Entity\Score;

class ScoresController extends AbstractController
{
    /**
     * @Route("/top-scores", name="show_scores")
     */
    public function showScores(ScoreRepository $scoreRepository): Response
    {
        // if (!$scores) {
        //     throw $this->createNotFoundException(
        //         'No scores found for id '.$id
        //     );
        // }
        $scoresGame21 = $scoreRepository->showAllSortedDesc("game21");
        $allScoresGame21 = array();
        foreach ($scoresGame21 as $score) {
            array_push($allScoresGame21, [$score->getPlayerName(), $score->getScore()]);
        }
        $scoresYatzee = $scoreRepository->showAllSortedDesc("yatzee");
        $allScoresYatzee = array();
        foreach ($scoresYatzee as $score) {
            array_push($allScoresYatzee, [$score->getPlayerName(), $score->getScore()]);
        }

        $data = [
            "header" => "Recommended in New Yorker",
            "scoresGame21" => $allScoresGame21,
            "scoresYatzee" => $allScoresYatzee,
        ];
        return $this->render('scores.html.twig', $data);
    }
}
