<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PostDocument
{
    public const TYPES = ['file', 'url'];

    #[Assert\NotBlank]
    public string $title;
    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::TYPES)]
    public string $type;

    #[Assert\NotBlank(groups: ['file'])]
    public ?string $filename;
    #[Assert\NotBlank(groups: ['file'])]
    public ?string $content;

    #[Assert\NotBlank(groups: ['url'])]
    #[Assert\Url(groups: ['url'])]
    public ?string $url;
    #[Assert\NotBlank(groups: ['url'])]
    public ?string $cssSelector;
}