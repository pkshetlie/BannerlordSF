<?php

namespace App\Controller\Backend;

use App\Entity\Challenge;
use App\Entity\Participation;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use Pkshetlie\PaginationBundle\Service\Calcul;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/challenge")
 */
class ChallengeController extends AbstractController
{
    /**
     * @Route("/", name="challenge_admin_index", methods={"GET"})
     * @param Request $request
     * @param ChallengeRepository $challengeRepository
     * @param Calcul $paginationService
     * @return Response
     */
    public function index(Request $request, ChallengeRepository $challengeRepository, Calcul $paginationService): Response
    {
        $qb = $challengeRepository->createQueryBuilder('c')
            ->orderBy('c.registrationOpening', 'DESC');
        $paginator = $paginationService->process($qb, $request);
        return $this->render('backend/challenge/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @Route("/new", name="challenge_admin_new", methods={"GET","POST"})
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        return $this->edit($request, new Challenge(), $slugger);
    }

    /**
     * @Route("/toggle-participation/{id}", name="challenge_admin_toggle_participation", methods={"GET","POST"})
     * @param Request $request
     * @param Participation $participation
     * @return Response
     */
    public function toggleParticipation(Request $request, Participation $participation): Response
    {
        $participation->setEnabled(!$participation->getEnabled());
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(['success' => true, 'replace' => $participation->getEnabled() ? "<i class='fas fa-check text-success'></i>" : "<i class='fas fa-times text-danger'></i>"]);
    }

    /**
     * @Route("/{id}/edit", name="challenge_admin_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Challenge $challenge
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function edit(Request $request, Challenge $challenge, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $banner = $form->get('banner')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($banner) {
                $originalFilename = pathinfo($banner->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $banner->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $banner->move(
                        $this->getParameter('challenge_banner_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $challenge->setBanner($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($challenge);
            $entityManager->flush();

            return $this->redirectToRoute('challenge_admin_index');
        }

        return $this->render('backend/challenge/create_edit.html.twig', [
            'challenge' => $challenge,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="challenge_admin_delete", methods={"GET"})
     * @param Request $request
     * @param Challenge $challenge
     * @return Response
     */
    public function delete(Request $request, Challenge $challenge): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($challenge);
        $entityManager->flush();

        return $this->redirectToRoute('challenge_admin_index');
    }
}
