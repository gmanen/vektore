<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PostQuestion
{
    #[Assert\NotBlank]
    public string $content;
}