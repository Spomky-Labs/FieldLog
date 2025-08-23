<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

final class EntryData
{
    #[Assert\NotBlank(groups: ['default'])]
    public string $title = '';

    #[Assert\NotBlank(groups: ['text'])]
    public ?string $text = null;

    #[Assert\NotBlank(groups: ['file'])]
    public ?File $file = null;

    #[Assert\NotBlank(groups: ['link'])]
    #[Assert\Url(groups: ['link'])]
    public ?string $url = null;
}
