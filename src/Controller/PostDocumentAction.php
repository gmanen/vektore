<?php

namespace App\Controller;

use App\Entity\Document;
use App\Exception\ValidationException;
use App\Message\DocumentCreated;
use App\Model\PostDocument;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/documents', methods: ['POST'])]
class PostDocumentAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly DocumentRepository $repository,
        private readonly MessageBusInterface $messageBus,
        private readonly string $projectDir,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var PostDocument $postDocument */
        $postDocument = $this->serializer->deserialize(
            $request->getContent(),
            PostDocument::class,
            'json'
        );

        if (count($errors = $this->validator->validate($postDocument, null , [$postDocument->type ?? 'file'])) > 0) {
            throw new ValidationException($errors);
        }

        $document = new Document();
        $document->setTitle($postDocument->title);
        $document->setType($postDocument->type);

        if ('file' === $postDocument->type) {
            file_put_contents($path = $this->projectDir.'/var/data/'.$postDocument->filename, base64_decode($postDocument->content));
            $document->setPath($path);
        } elseif ('url' === $postDocument->type) {
            $document->setPath($postDocument->url);
            $document->setSelector($postDocument->cssSelector);
        }

        $this->repository->persistAndFlush($document);

        $this->messageBus->dispatch(new DocumentCreated($document->getId()));

        return new Response($this->serializer->serialize($postDocument, 'json'), Response::HTTP_CREATED);
    }
}