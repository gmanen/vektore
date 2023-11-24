<?php

namespace App\OpenAI;

use OpenAI\Client;
use OpenAI\Responses\StreamResponse;

class Chat
{
    public function __construct(
        private readonly Client $client,
        private readonly string $modelName = 'gpt-3.5-turbo',
    ) {
    }

    public function sendQuestion(string $input, array $embeddings, bool $streamed = false): string|StreamResponse
    {
        $context = '';

        foreach ($embeddings as $document) {
            $context .= <<<CTX

Document : {$document['title']}
Contenu : {$document['content']}

CTX;
        }

        $params = [
            'model' => $this->modelName,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => <<<MSG
Tu joues le rôle d'une base de connaissance interactive.
Les utilisateurs vont te poser des questions auxquelles tu devras essayer de répondre en essayant de rester dans le contexte de la base documentaire.
Dans la mesure du possible, cite directement les passages des documents qui te permettent de répondre à la question.
Tu peux enrichir les réponses avec tes connaissances personnelles, mais attention à ne pas trop t'éloigner du contexte et préciser ce qui relève de la base documentaire ou non.
La base documentaire a été interrogée pour retourner les résultats suivants:
$context
MSG
                ],
                [
                    'role' => 'user',
                    'content' => $input,
                ]
            ],
        ];

        if ($streamed) {
            return $this->client->chat()->createStreamed($params);
        }

        $response = $this->client->chat()->create($params);

        return $response->choices[0]->message->content ?? '';
    }
}