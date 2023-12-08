<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/documents/{id}', methods: ['GET'])]
class GetDocumentAction
{
    public function __construct(
        private readonly DocumentRepository $documentRepository,
    ) {
    }

    public function __invoke(string $id): Response
    {
        $document = $this->documentRepository->find($id);

        $response = new BinaryFileResponse($document->getRawPath());
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($document->getRawPath())
        );

        return $response;
    }
}
