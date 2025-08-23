<?php

declare(strict_types=1);

namespace App\Entity;

enum TypeEnum: string
{
    case TEXT = 'text';
    case LINK = 'link';
    case FILE = 'file';
}
