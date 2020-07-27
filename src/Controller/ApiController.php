<?php


namespace App\Controller;


use App\Entity\UserScore;
use App\Repository\UserScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/points/{apiKey}/{points}",name="api_points")
     */
    public function api(Request $request)
    {
        $apiKey = $request->get('apiKey');
        $points = $request->get('points');
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserScore $api */
        $api = $entityManager->getRepository(UserScore::class)->findOneBy(["apiKey" => $apiKey]);
        if ($api != null) {
            $api->setPoints($points);
            $entityManager->flush();
        }
        return new Response('OK');
    }
    /**
     * @Route("/api/",name="api_index")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserScore $api */
        $api = $entityManager->getRepository(UserScore::class)->findOneBy(['apiKey'=> "0ddd63424b5af"]);

        return $this->render('Frontend/Api/index.html.twig',['api'=>$api]);
    }
}