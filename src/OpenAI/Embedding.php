<?php

namespace App\OpenAI;

use OpenAI\Client;

class Embedding
{
    public function __construct(
        private readonly Client $client,
        private readonly string $modelName = 'text-embedding-ada-002',
    ) {
    }

    /**
     * @return float[]
     */
    public function getEmbedding(string $input): array
    {
        $response = $this->client->embeddings()->create([
            'model' => $this->modelName,
            'input' => $input,
        ]);

        return $response->embeddings[0]->embedding;
    }
}