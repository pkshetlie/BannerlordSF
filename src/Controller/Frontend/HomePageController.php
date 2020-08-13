<?php


namespace App\Controller\Frontend;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/",name="home")
     */
    public function index()
    {
        return $this->render('frontend/home_page/index.html.twig');
    }
}