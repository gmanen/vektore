<?php

namespace App\MessageHandler;

use App\Message\DocumentCreated;
use App\OpenAI\Embedding;
use App\Repository\DocumentRepository;
use App\VectoreStore\RedisStore;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DocumentEmbedding
{
    public function __construct(
        private readonly DocumentRepository $repository,
        private readonly RedisStore $redisStore,
        private readonly Embedding $embedding,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function __invoke(DocumentCreated $message): void
    {
        $document = $this->repository->find($message->id);
        $content = file_get_contents($document->getPath());
        $chunks = [];
        $currentChunk = '';

        foreach (explode("\n", $content) as $lines) {
            foreach (explode('.', $lines) as $phrase) {
                if ($currentChunk === '') {
                    $currentChunk = $phrase;
                    continue;
                }

                if ($currentChunk . $phrase >= 1000) {
                    $chunks[] = $currentChunk;
                    $currentChunk = '';

                    continue;
                }

                $currentChunk .= $phrase;
            };
        }

        $chunkIndex = 0;

        foreach ($chunks as $chunk) {
            $chunk = str_replace("\n", ' ', $chunk);
            $this->redisStore->addDocument($document, $chunk, $this->embedding->getEmbedding($chunk), $chunkIndex);
            $chunkIndex++;
        }
    }
}