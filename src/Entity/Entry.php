<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EntryRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EntryRepository::class)]
class Entry
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        #[ORM\Column]
        public string $id,
        #[ORM\Column(length: 180)]
        #[Assert\NotBlank]
        #[Assert\Length(max: 180)]
        public string $title,
        #[ORM\Column]
        public DateTimeImmutable $publishedAt,
        #[ORM\Column(length: 16)]
        #[Assert\NotBlank]
        public TypeEnum $type,
        #[ORM\Column(nullable: true)]
        public ?string $filePath,
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        public ?string $textContent,
        #[ORM\Column(type: Types::STRING, length: 2048, nullable: true)]
        #[Assert\Url]
        public ?string $url,
        #[ORM\Column(length: 255, nullable: true)]
        public ?string $mimeType,
    ) {
    }

    public static function createUrl(string $id, string $title, DateTimeImmutable $publishedAt, string $url): self
    {
        return new self($id, $title, $publishedAt, TypeEnum::LINK, null, null, $url, null);
    }

    public static function createText(
        string $id,
        string $title,
        DateTimeImmutable $publishedAt,
        string $textContent
    ): self {
        return new self($id, $title, $publishedAt, TypeEnum::TEXT, null, $textContent, null, null);
    }

    public static function createFile(
        string $id,
        string $title,
        DateTimeImmutable $publishedAt,
        string $filePath,
        ?string $mimeType
    ): self {
        return new self($id, $title, $publishedAt, TypeEnum::FILE, $filePath, null, null, $mimeType);
    }
}
