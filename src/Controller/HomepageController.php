<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    public function __construct(
        private readonly EntryRepository $entryRepository
    ) {
    }

    #[Route('/', methods: [Request::METHOD_GET])]
    public function root(): Response
    {
        return $this->render('homepage/index.html.twig');
    }

    #[Route('/list', name: 'list', methods: [Request::METHOD_GET])]
    public function list(Request $request): Response
    {
        $page = (int) $request->query->get('page', '1');
        $page = max($page, 1);
        $maxPerPage = 25;

        return $this->render('homepage/list.html.twig', [
            'entries' => $this->entryRepository->getPaginatedEntries($page, $maxPerPage),
            'current_page' => $page,
            'max_per_page' => $maxPerPage,
        ]);
    }
}
