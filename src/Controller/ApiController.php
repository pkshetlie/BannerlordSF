<?php


namespace App\Controller;


use App\Entity\UserScore;
use App\Service\ChallengeService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

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
     * @Route("/api/test",name="api_index")
     */
    public function index(Request $request, \Swift_Mailer $mailer)
    {
        VarDumper::dump($request);
        $message = (new \Swift_Message('API bannerlord'))
            ->setFrom($this->getParameter('webmaster_email'))
            ->setTo("pierrick.pobelle@gmail.com")
            ->setBody(
                json_encode($request->headers->all())."\r\n"
                .json_encode($request->query)."\r\n"
                .json_encode($_POST)."\r\n"
                .json_encode($_REQUEST)."\r\n"
                ,
                'text/html'
            );
        try {

            $mailer->send($message);
        } catch (\Exception $e) {
            $x = $e;
        }
        return new Response("<body>OK</body>");
    }
}