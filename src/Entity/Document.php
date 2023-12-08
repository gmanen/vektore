<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Document
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $title;

    #[ORM\Column(type: 'string')]
    private string $type = 'file';

    #[ORM\Column(type: 'string')]
    private string $txtPath;

    #[ORM\Column(type: 'string')]
    private string $rawPath;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $selector;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getTxtPath(): string
    {
        return $this->txtPath;
    }

    public function setTxtPath(string $txtPath): void
    {
        $this->txtPath = $txtPath;
    }

    public function getRawPath(): string
    {
        return $this->rawPath;
    }

    public function setRawPath(string $rawPath): void
    {
        $this->rawPath = $rawPath;
    }

    public function getSelector(): ?string
    {
        return $this->selector;
    }

    public function setSelector(?string $selector): void
    {
        $this->selector = $selector;
    }
}