<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Repository\UserRepository;
use Pkshetlie\PaginationBundle\Service\Calcul;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_admin_index", methods={"GET"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param Calcul $pagination
     * @return Response
     */
    public function index(Request $request, UserRepository $userRepository, Calcul $pagination): Response
    {
        $qb = $userRepository
            ->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->addOrderBy("u.id", "DESC");
        $paginator = $pagination->setDefaults(50)->process($qb, $request);

        return $this->render('backend/user/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @Route("/new", name="user_admin_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->edit($request, new Rule());
    }


    /**
     * @Route("/{id}/edit", name="user_admin_edit", methods={"GET","POST"})
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
     * @Route("/{id}", name="user_admin_delete", methods={"GET"})
     * @param User $rule
     * @return Response
     */
    public function delete(User $rule): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($rule);
        $entityManager->flush();
        return $this->redirectToRoute('user_admin_index');
    }
}
