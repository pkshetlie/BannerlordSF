<?php

namespace App\Controller\Backend;

use App\Entity\Rule;
use App\Form\RuleType;
use App\Repository\RuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/rule")
 */
class RuleController extends AbstractController
{
    /**
     * @Route("/", name="rule_index", methods={"GET"})
     * @param RuleRepository $ruleRepository
     * @return Response
     */
    public function index(RuleRepository $ruleRepository): Response
    {
        return $this->render('backend/rule/index.html.twig', [
            'rules' => $ruleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="rule_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->edit($request, new Rule());
    }


    /**
     * @Route("/{id}/edit", name="rule_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Rule $rule
     * @return Response
     */
    public function edit(Request $request, Rule $rule): Response
    {
        $form = $this->createForm(RuleType::class, $rule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('rule_index');
        }

        return $this->render('backend/rule/create_edit.html.twig', [
            'rule' => $rule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="rule_delete", methods={"DELETE"})
     * @param Request $request
     * @param Rule $rule
     * @return Response
     */
    public function delete(Request $request, Rule $rule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($rule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('rule_index');
    }
}
