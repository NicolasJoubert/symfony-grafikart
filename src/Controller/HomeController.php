<?php

namespace App\Controller;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response
     */
    #[NoReturn] #[Route("/", name: "home")]
    function index(Request $request): Response
    {

        return $this->render('home/index.html.twig');
    }

}
