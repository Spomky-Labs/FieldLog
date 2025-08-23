<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Entry;
use App\Entity\TypeEnum;
use App\Form\EntryFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EntryController extends AbstractController
{
    public function __construct(
        private readonly EntryFormHandler $textFormHandler,
    ) {
    }

    #[Route('/new/{type}', name: 'entry_new', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function new(Request $request, string $type): Response
    {
        $form = $this->textFormHandler->create([
            'type' => TypeEnum::from($type),
        ]);
        $result = $this->textFormHandler->handle($request, $form);
        if ($result instanceof Entry) {
            $this->addFlash('success', 'entry.text.created_successfully');
            return $this->redirectToRoute('app_homepage__invoke');
        }

        return $this->render('entry/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'entry_show', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function show(Entry $entry): Response
    {
        return $this->render('entry/show.html.twig', [
            'entry' => $entry,
        ]);
    }
}
