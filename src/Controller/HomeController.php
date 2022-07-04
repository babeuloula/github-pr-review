<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class HomeController
{
    public function __construct(readonly private Environment $twig)
    {
    }

    #[Route('/', name: 'home', methods: Request::METHOD_GET)]
    public function index(): Response
    {
        return new Response(
            $this->twig->render('home.html.twig')
        );
    }
}
