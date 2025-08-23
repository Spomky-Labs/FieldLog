<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route('/')]
    public function __invoke(): Response
    {
        return $this->render('homepage/index.html.twig', [
            'entries' => [],
        ]);
    }
}
