<?php


namespace App\Controller;


use App\Entity\UserScore;
use App\Repository\UserScoreRepository;
use App\Service\ChallengeService;
use Doctrine\ORM\EntityManager;
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
    public function api(string $apiKey,int $run, int $points, ChallengeService $challengeService)
    {
/** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        /** @var UserScore $api */
        $api = $entityManager->getRepository(UserScore::class)->createQueryBuilder('us')
->join('us.user','user')
->where('user.apiKey = :apikey')
            ->andWhere("us.challengeEdition = :edition")
            ->andWhere("us.runNumber = :run")
            ->setParameter('apikey',$apiKey)
            ->setRun()
            ->getQuery()->getOneOrNullResult();


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