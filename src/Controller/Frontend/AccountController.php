<?php

namespace App\Controller\Frontend;

use App\Entity\Challenge;
use App\Entity\ChallengeNewsletter;
use App\Entity\Participation;
use App\Entity\User;
use App\Form\ChallengeType;
use App\Helper\XmlResponse;
use App\Repository\ChallengeNewsletterRepository;
use App\Repository\ChallengeRepository;
use App\Repository\ParticipationRepository;
use Pkshetlie\PaginationBundle\Service\Calcul;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="my_account", methods={"GET"})
     * @param Request $request
     * @param ChallengeRepository $challengeRepository
     * @param Calcul $paginationService
     * @return Response
     */
    public function index(Request $request, ChallengeRepository $challengeRepository, Calcul $paginationService): Response
    {

        return $this->render('frontend/account/index.html.twig', [
        ]);
    }

    /**
     * @Route("/gen/file", name="user_generate_apikey_file")
     *
     */
    public function genFile()
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->getUser()->getApiKey() == null) {
            $this->getUser()->setApiKey(md5($this->getUser()->getUsername() . $this->getUser()->getEmail() . date('d_m_y_s')));
            $em->flush();
        }

        $response = new XmlResponse("<configuration>
    <apiKey value='" . $this->getUser()->getApiKey() . "'/>
</configuration>");
        $response->headers->add([
            "Content-Disposition" => ResponseHeaderBag::DISPOSITION_ATTACHMENT . "; filename=\"Key.xml\"",
        ]);

        return $response;

    }
}
