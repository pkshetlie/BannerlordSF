<?php

namespace App\Controller\Backend;

use App\Entity\Challenge;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/challenge")
 */
class ChallengeController extends AbstractController
{
    /**
     * @Route("/", name="challenge_index", methods={"GET"})
     * @param ChallengeRepository $challengeRepository
     * @return Response
     */
    public function index(ChallengeRepository $challengeRepository): Response
    {
        return $this->render('backend/challenge/index.html.twig', [
            'challenges' => $challengeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="challenge_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->edit($request, new Challenge());
    }

    /**
     * @Route("/{id}/edit", name="challenge_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Challenge $challenge
     * @return Response
     */
    public function edit(Request $request, Challenge $challenge): Response
    {
        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($challenge);
            $entityManager->flush();

            return $this->redirectToRoute('challenge_index');
        }

        return $this->render('backend/challenge/create_edit.html.twig', [
            'challenge' => $challenge,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="challenge_delete", methods={"GET"})
     * @param Request $request
     * @param Challenge $challenge
     * @return Response
     */
    public function delete(Request $request, Challenge $challenge): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($challenge);
        $entityManager->flush();

        return $this->redirectToRoute('challenge_index');
    }
}
