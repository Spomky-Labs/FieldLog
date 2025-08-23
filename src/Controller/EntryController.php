<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EntryController extends AbstractController
{
    #[Route('/new', name: 'entry_new')]
    public function new(): Response
    {
        return $this->render('entry/new.html.twig');
    }
}
