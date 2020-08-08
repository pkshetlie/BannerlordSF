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
        return $this->render('Frontend/home_page/index.html.twig');
    }
}