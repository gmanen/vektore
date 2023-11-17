<?php

namespace App\VectoreStore;

use App\Entity\Document;
use Predis\Client;
use Predis\Command\Argument\Search\CreateArguments;
use Predis\Command\Argument\Search\SchemaFields\NumericField;
use Predis\Command\Argument\Search\SchemaFields\TextField;
use Predis\Command\Argument\Search\SchemaFields\VectorField;
use Predis\Command\Argument\Search\SearchArguments;
use Predis\Response\ServerException;

class RedisStore
{
    public function __construct(
        private readonly Client $client,
        private readonly string $redisIndex = 'vectorstore',
        private readonly int $documentsPerQuery = 4,
    ) {

    }

    public function addDocument(Document $document, string $chunk, array $chunkEmbedding, int $chunkIndex): void
    {
        $indexed = [
            $this->redisIndex.':'.$document->getId()->toRfc4122().':'.$chunkIndex,
            '$',
            json_encode([
                'content' => $chunk,
                'title' => $document->getTitle(),
                'type' => $document->getType(),
                'chunkNumber' => $chunkIndex,
                'embedding' => $chunkEmbedding,
            ]),
        ];

        $this->client->jsonmset(...$indexed);
    }

    /**
     * @throws ServerException
     * @throws \JsonException
     */
    public function similaritySearch(array $questionEmbedding): array
    {
        try {
            $this->client->ftinfo($this->redisIndex);
        } catch (ServerException $e) {
            if (strtolower($e->getMessage()) !== 'unknown index name') {
                throw $e;
            }

            $this->createIndex(count($questionEmbedding));
        }

        $binaryQueryVector = '';

        foreach ($questionEmbedding as $value) {
            $binaryQueryVector .= pack('f', $value);
        }

        /** @var array{0: int, 1: string, 2: string[]} $rawResults */
        $rawResults = $this->client->ftsearch(
            $this->redisIndex,
            "(*)=>[KNN {$this->documentsPerQuery} @embedding \$query_vector AS distance]",
            (new SearchArguments())
                ->dialect('2')
                ->params(['query_vector', $binaryQueryVector])
                ->sortBy('distance', 'ASC')
        );

        $documents = [];
        $rawRedisResultsCount = count($rawResults);

        for ($i = 1; $i < $rawRedisResultsCount; $i += 2) {
            [$distanceLabel, $distanceValue, $redisPath, $jsonEncodedDocument] = $rawResults[$i + 1];
            /** @var array{content: string, document: string, title: string, type: string, chunkNumber: int, embedding: float[]} $data */
            $data = json_decode($jsonEncodedDocument, true, 512, JSON_THROW_ON_ERROR);
            $data['document_chunk'] = $redisPath;
            $documents[] = $data;
        }

        return $documents;
    }

    private function createIndex(int $vectorDimension): void
    {
        $schema = [
            new TextField('$.content', 'content'),
            new TextField('$.title', 'title'),
            new TextField('$.type', 'type'),
            new NumericField('$.chunkNumber', 'chunkNumber'),
            new VectorField('$.embedding', 'FLAT', [
                'DIM', $vectorDimension,
                'TYPE', 'FLOAT32',
                'DISTANCE_METRIC', 'COSINE',
            ], 'embedding'),
        ];

        $this->client->ftcreate($this->redisIndex, $schema,
            (new CreateArguments())
                ->on('JSON')
                ->prefix([$this->redisIndex.':'])
        );
    }
}