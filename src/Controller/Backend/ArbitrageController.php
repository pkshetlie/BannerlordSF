<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/arbitrage")
 */
class ArbitrageController extends AbstractController
{
    /**
     * @Route("/", name="arbitrage_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        return $this->render('backend/arbitrage/index.html.twig', [
            'participations' => $user->getArbitreOf()
        ]);
    }
}
