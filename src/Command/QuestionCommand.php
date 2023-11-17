<?php

namespace App\Command;

use LLPhant\Chat\OpenAIChat;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAIEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Redis\RedisVectorStore;
use LLPhant\OpenAIConfig;
use LLPhant\Query\SemanticSearch\QuestionAnswering;
use Predis\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:question')]
class QuestionCommand extends Command
{
    public function __construct(
        #[Autowire('@snc_redis.default')]
        private readonly Client $redisClient,
        #[Autowire('%openai.api_key%')]
        private readonly string $openAIApiKey,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        parent::configure();

        $this->addArgument('question', InputArgument::REQUIRED, 'Question to ask');
    }

    /**
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = new OpenAIConfig();
        $config->apiKey = $this->openAIApiKey;
        $embeddingGenerator = new OpenAIEmbeddingGenerator($config);
        $vectorStore = new RedisVectorStore($this->redisClient);

        $qa = new QuestionAnswering(
            $vectorStore,
            $embeddingGenerator,
            new OpenAIChat($config)
        );

        $output->writeln($qa->answerQuestion($input->getArgument('question')));

        return Command::SUCCESS;
    }
}