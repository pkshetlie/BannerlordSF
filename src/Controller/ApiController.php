<?php


namespace App\Controller;


use App\Entity\Run;
use App\Entity\UserScore;
use App\Service\ChallengeService;
use App\Service\RunService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/user/{apiKey}",name="api_points",methods={"POST"})
     */
    public function api(Request $request, string $apiKey, RunService $runService)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        /** @var Run $run */
        $run = $entityManager->getRepository(Run::class)
            ->createQueryBuilder('r')
            ->leftJoin("r.challenge", 'challenge')
            ->leftJoin("challenge.challengeDates", 'challenge_dates')
            ->leftJoin("r.user", 'user')
            ->where('user.apiKey = :apikey')
            ->andWhere('challenge_dates.startDate >= :now')
            ->andWhere('challenge_dates.endDate <= :now')
            ->setParameter("apikey", $apiKey)
            ->setParameter("now", new \DateTime())
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->setFirstResult(0)
            ->getQuery()->getOneOrNullResult();
        if ($run != null) {
            $score = (array)json_decode($request->getContent());
            foreach ($run->getRunSettings() as $runSetting) {
                if (isset($score[$runSetting->getChallengeSetting()->getAutoValue()])) {
                    $runSetting->setValue($score[$runSetting->getChallengeSetting()->getAutoValue()]);
                    $entityManager->flush();
                }
            }
            $runService->ComputeScore($run);
            $entityManager->flush();
        }
        return new Response('OK');
    }

    /**
     * @Route("/api/end-run/{apiKey}",name="api_end_run",methods={"POST"})
     */
    public function apiEndRun(Request $request, string $apiKey, RunService $runService)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Run $run */
        $run = $entityManager->getRepository(Run::class)
            ->createQueryBuilder('r')
            ->leftJoin("r.challenge", 'challenge')
            ->leftJoin("challenge.challengeDates", 'challenge_dates')
            ->leftJoin("r.user", 'user')
            ->where('user.apiKey = :apikey')
            ->andWhere('challenge_dates.startDate >= :now')
            ->andWhere('challenge_dates.endDate <= :now')
            ->setParameter("apikey", $apiKey)
            ->setParameter("now", new \DateTime())
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->setFirstResult(0)
            ->getQuery()->getOneOrNullResult();
        if ($run != null) {
            $runService->endOfRun($run);
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
                json_encode() . "\r\n"
                . json_encode($request->query) . "\r\n"
                . json_encode($_POST) . "\r\n"
                . json_encode($_REQUEST) . "\r\n"
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