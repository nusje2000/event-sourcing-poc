<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class IndexController
{
    public function __construct(private Environment $twig) {}

    public function __invoke(): Response
    {
        return new Response($this->twig->render('index.html.twig'));
    }
}
