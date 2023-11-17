<?php

namespace App\Command;

use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAIEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Redis\RedisVectorStore;
use LLPhant\OpenAIConfig;
use Predis\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:embeddings:create')]
class CreateEmbeddingsCommand extends Command
{
    public function __construct(
        #[Autowire('@snc_redis.default')]
        private readonly Client $redisClient,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        #[Autowire('%openai.api_key%')]
        private readonly string $openAIApiKey,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        parent::configure();

        $this->addOption('data_path', 'd', InputOption::VALUE_OPTIONAL, 'Path to the data files', 'var/data');
    }

    /**
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->redisClient->flushall();

        $config = new OpenAIConfig();
        $config->apiKey = $this->openAIApiKey;
        $embeddingGenerator = new OpenAIEmbeddingGenerator($config);
        $vectorStore = new RedisVectorStore($this->redisClient);

        $dataPath = $input->getOption('data_path');

        if (!str_starts_with($dataPath, '/')) {
            $dataPath = $this->projectDir . '/' . $dataPath;
        }

        $dataReader = new FileDataReader($dataPath);
        $documents = $dataReader->getDocuments();
        $splitDocuments = DocumentSplitter::splitDocuments($documents);
        $embeddedDocuments = $embeddingGenerator->embedDocuments($splitDocuments);

        $vectorStore->addDocuments($embeddedDocuments);

        return Command::SUCCESS;
    }
}
