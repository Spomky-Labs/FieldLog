<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Entry;
use App\Entity\TypeEnum;
use App\Repository\EntryRepository;
use DateTimeImmutable;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Ulid;

final readonly class EntryFormHandler
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EntryRepository $entryRepository,
        private ClockInterface $clock,
        private FilesystemOperator $defaultFilesystem,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     * @return FormInterface<EntryType>
     */
    public function create(array $options = []): FormInterface
    {
        return $this->formFactory->create(EntryType::class, null, $options);
    }

    /**
     * @param FormInterface<EntryType> $form
     */
    public function handle(Request $request, FormInterface $form): null|Entry
    {
        $form->handleRequest($request);
        if (! $form->isSubmitted() || ! $form->isValid()) {
            return null;
        }

        /** @var EntryData $data */
        $data = $form->getData();
        /** @var TypeEnum $type */
        $type = $form->getConfig()
            ->getOption('type');
        $now = $this->clock->now();
        $id = Ulid::generate();

        $entry = match ($type) {
            TypeEnum::TEXT => Entry::createText($id, $data->title, $now, $data->text),
            TypeEnum::LINK => Entry::createUrl($id, $data->title, $now, $data->url),
            TypeEnum::FILE => $this->handleFileUpload($id, $data, $now),
        };

        $this->entryRepository->save($entry);

        return $entry;
    }

    private function handleFileUpload(string $id, EntryData $data, DateTimeImmutable $now): Entry
    {
        $file = $data->file;
        $filename = $id . '.' . $file->guessExtension();
        $mimeType = $file->getMimeType();

        $this->defaultFilesystem->write($filename, file_get_contents($file->getPathname()));

        return Entry::createFile($id, $data->title, $now, $filename, $mimeType);
    }
}
