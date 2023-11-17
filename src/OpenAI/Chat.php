<?php

namespace App\OpenAI;

use OpenAI\Client;

class Chat
{
    public function __construct(
        private readonly Client $client,
        private readonly string $modelName = 'gpt-3.5-turbo',
    ) {
    }

    public function sendQuestion(string $input, array $embeddings): string
    {
        $context = '';

        foreach ($embeddings as $document) {
            $context .= <<<CTX

Document : {$document['title']}
Contenu : {$document['content']}

CTX;
        }

        $response = $this->client->chat()->create([
            'model' => $this->modelName,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => <<<MSG
Tu joues le rôle d'une base de connaissance interactive.
Les utilisateurs vont te poser des questions auxquelles tu devras essayer de répondre en essayant de rester dans le contexte de la base documentaire.
La base documentaire a été interrogée pour retourner les résultats suivants:
$context
MSG
                ],
                [
                    'role' => 'user',
                    'content' => $input,
                ]
            ],
        ]);

        return $response->choices[0]->message->content ?? '';
    }
}