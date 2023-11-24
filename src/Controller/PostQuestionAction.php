<?php

namespace App\Controller;

use App\Exception\ValidationException;
use App\Model\PostQuestion;
use App\OpenAI\Chat;
use App\OpenAI\Embedding;
use App\VectoreStore\RedisStore;
use OpenAI\Client;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use Predis\Response\ServerException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/question', name: 'post_question', methods: ['POST'])]
class PostQuestionAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly Embedding $embedding,
        private readonly RedisStore $redisStore,
        private readonly Chat $chat,
    ) {
    }

    /**
     * @throws \JsonException
     * @throws ServerException
     */
    public function __invoke(Request $request): Response
    {
        $question = $this->serializer->deserialize(
            $request->getContent(),
            PostQuestion::class,
            'json'
        );

        if (count($errors = $this->validator->validate($question)) > 0) {
            throw new ValidationException($errors);
        }

        $questionEmbedding = $this->embedding->getEmbedding($question->content);
        $documents = $this->redisStore->similaritySearch($questionEmbedding);

        if ($documents === []) {
            return new JsonResponse(['answer' => "Je n'ai pas trouvé l'information dans ma base de connaissance. Essayez de reformuler votre question."]);
        }

        $stream = $this->chat->sendQuestion($question->content, $documents, true);

        $response = new StreamedResponse(function () use ($stream) {
            /** @var CreateStreamedResponse $response */
            foreach ($stream as $response) {
                echo $response->choices[0]->delta->content ?? '';
                flush();
            }
        });

        $response->send();

        return $response;
    }
}