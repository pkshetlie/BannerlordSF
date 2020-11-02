<?php

namespace App\Controller\Backend;

use App\Entity\Challenge;
use App\Entity\ChallengeSetting;
use App\Entity\Participation;
use App\Entity\User;
use App\Form\ChallengeSettingType;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Pkshetlie\PaginationBundle\Service\Calcul;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/challenge/setting")
 */
class ChallengeSettingController extends AbstractController
{

    /**
     * @Route("/{id}", name="challenge_admin_settings_index")
     * @param Request $request
     * @param Challenge $challenge
     * @return Response
     */
    public function settings(Request $request, Challenge $challenge)
    {

        return $this->render('backend/setting/index.html.twig', [
            'challenge' => $challenge
        ]);
    }

    /**
     * @Route("/new/{id}", name="challenge_admin_setting_new")
     * @param Request $request
     * @param Challenge $challenge
     * @return Response
     */
    public function newSettings(Request $request, Challenge $challenge)
    {
        $challengeSetting = new ChallengeSetting();
        $challengeSetting->setChallenge($challenge);
        return $this->editSettings($request, $challengeSetting);
    }

    /**
     * @Route("/edit/{id}", name="challenge_admin_setting_edit")
     * @param Request $request
     * @param ChallengeSetting $setting
     * @return Response
     */
    public function editSettings(Request $request, ChallengeSetting $setting)
    {
        $form = $this->createForm(ChallengeSettingType::class, $setting);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager  =  $this->getDoctrine()->getManager();
            $entityManager->persist($setting);
            $entityManager->flush();
            $this->addFlash('success','Parametrage barème enregistré');
        }

        return $this->render('backend/setting/create_edit.html.twig', [
            'form' => $form->createView(),
            'challenge' => $setting->getChallenge(),
        ]);
    }
}
