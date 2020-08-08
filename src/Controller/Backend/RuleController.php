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
     */
    public function index(RuleRepository $ruleRepository): Response
    {
        return $this->render('backend/rule/index.html.twig', [
            'rules' => $ruleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="rule_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $rule = new Rule();
        $form = $this->createForm(RuleType::class, $rule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rule);
            $entityManager->flush();

            return $this->redirectToRoute('rule_index');
        }

        return $this->render('backend/rule/new.html.twig', [
            'rule' => $rule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="rule_show", methods={"GET"})
     */
    public function show(Rule $rule): Response
    {
        return $this->render('backend/rule/show.html.twig', [
            'rule' => $rule,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="rule_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Rule $rule): Response
    {
        $form = $this->createForm(RuleType::class, $rule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('rule_index');
        }

        return $this->render('backend/rule/edit.html.twig', [
            'rule' => $rule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="rule_delete", methods={"DELETE"})
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
