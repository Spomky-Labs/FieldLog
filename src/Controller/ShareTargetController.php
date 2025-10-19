<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Entry;
use App\Entity\TypeEnum;
use App\Repository\EntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;
use League\Flysystem\FilesystemOperator;

class ShareTargetController extends AbstractController
{
    public function __construct(
        private readonly EntryRepository    $entryRepository,
        private readonly FilesystemOperator $defaultFilesystem,
        private readonly ClockInterface $clock,
    ) {
    }

    #[Route('/share-target', name: 'app_share_target', methods: ['POST'])]
    public function handleSharedContent(Request $request): Response
    {
        // Get shared data from the request
        $title = $request->request->get('title', '');
        $text = $request->request->get('text', '');
        $url = $request->request->get('url', '');

        /** @var UploadedFile[] $files */
        $files = $request->files->get('files', []);

        // Ensure $files is always an array
        if (!is_array($files)) {
            $files = [$files];
        }

        // Filter out empty files
        $files = array_filter($files, static fn($file) => $file instanceof UploadedFile);

        // Determine the entry type based on what was shared
        $entryType = $this->determineEntryType($text, $url, $files);

        // Set title - use shared title or generate one
        if (empty($title)) {
            $title = $this->generateTitle($entryType, $text, $url);
        }

        // Create entry based on type
        $entry = match ($entryType) {
            TypeEnum::FILE => $this->createFileEntries($title, $files, $text),
            TypeEnum::LINK => $this->createLinkEntry($title, $url, $text),
            TypeEnum::TEXT => $this->createTextEntry($title, $text, $url),
        };

        // Persist the entry
        $this->entryRepository->save($entry);

        // Add success flash message
        $this->addFlash('success', 'Content shared to FieldLog successfully!');

        // Return 303 See Other redirect (best practice for POST requests)
        return $this->redirectToRoute('entry_show', [
            'id' => $entry->id
        ], Response::HTTP_SEE_OTHER);
    }

    /**
     * Determine entry type based on shared content
     */
    private function determineEntryType(string $text, string $url, array $files): TypeEnum
    {
        // If files are shared, it's a file entry
        if (!empty($files)) {
            return TypeEnum::FILE;
        }

        // If URL is shared (and no files), it's a link entry
        if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            return TypeEnum::LINK;
        }

        // Otherwise, it's a text entry
        return TypeEnum::TEXT;
    }

    /**
     * Generate a title based on entry type and content
     */
    private function generateTitle(TypeEnum $type, string $text, string $url): string
    {
        $timestamp = $this->clock->now()->format('Y-m-d H:i');

        // Try to extract a meaningful title from content
        if ($type === TypeEnum::TEXT && !empty($text)) {
            // Use first line or first 50 characters as title
            $lines = explode("\n", $text);
            $firstLine = trim($lines[0]);
            if (!empty($firstLine)) {
                return mb_substr($firstLine, 0, 180);
            }
        }

        if ($type === TypeEnum::LINK && !empty($url)) {
            // Try to extract domain name
            $domain = parse_url($url, PHP_URL_HOST);
            if ($domain) {
                return "Shared from {$domain}";
            }
        }

        return match ($type) {
            TypeEnum::FILE => "Shared file - {$timestamp}",
            TypeEnum::LINK => "Shared link - {$timestamp}",
            TypeEnum::TEXT => "Shared note - {$timestamp}",
        };
    }

    /**
     * Create file entry
     */
    private function createFileEntries(
        string $title,
        array $files,
        string $text
    ): Entry {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }
            /** @var UploadedFile $file */

            // Generate safe filename
            $id = Ulid::generate();
            $filename = $id . '.' . $file->guessExtension();
            $mimeType = $file->getMimeType();

            // Move file to uploads directory
            $this->defaultFilesystem->write($filename, file_get_contents($file->getPathname()));

            // Create entry
            $entry = Entry::createFile(
                $id,
                $title,
                $this->clock->now(),
                $filename,
                $mimeType
            );

            // Add text as description if provided
            if (!empty($text)) {
                // Since Entry uses public properties, we can set textContent directly
                $entry->textContent = $text;
            }

            return $entry;
        }
    }

    /**
     * Create link entry
     */
    private function createLinkEntry(
        string $title,
        string $url,
        string $text
    ): Entry {
        $entry = Entry::createUrl(Ulid::generate(), $title, $this->clock->now(), $url);

        // Use text as additional description
        if (!empty($text)) {
            $entry->textContent = $text;
        }

        return $entry;
    }

    /**
     * Create text entry
     */
    private function createTextEntry(
        string $title,
        string $text,
        string $url
    ): Entry {
        $content = $text;

        // If URL is also provided, append it to the text
        if (!empty($url)) {
            $content .= "\n\nSource: " . $url;
        }

        return Entry::createText(Ulid::generate(), $title, $this->clock->now(), $content);
    }
}
