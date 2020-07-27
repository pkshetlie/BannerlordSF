<?php


namespace App\Controller;


use App\Entity\UserScore;
use App\Repository\UserScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/points/{apiKey}/{points}",name="home")
     */
    public function api(string $apiKey, int $points)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserScore $api */
        $api = $entityManager->getRepository(UserScore::class)->findBy(["apiKey" => $apiKey]);
        if ($api != null) {
            $api->setPoints($points);
            $entityManager->flush();
        }
        return new Response('OK');
    }
    public function index(string $apiKey, int $points)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserScore $api */
        $api = $entityManager->getRepository(UserScore::class)->findBy(['apiKey'=> "0ddd63424b5af786fdd47bcc9209bcf3"]);

        return $this->render('Frontend/Api/index.html.twig',['scores'=>$api]);
    }
}