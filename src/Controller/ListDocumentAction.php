<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/documents', methods: ['GET'])]
class ListDocumentAction
{
    public function __construct(
        private readonly DocumentRepository $repository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(): Response
    {
        return new Response($this->serializer->serialize($this->repository->findAll(), 'json'));
    }
}
