<?php

namespace App\Command;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(name: 'app:documents:create-web')]
class CreateWebDocumentsCommand extends Command
{
    public function __construct(
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        parent::configure();

        $this->addArgument('url', InputArgument::REQUIRED, 'URL to crawl');
        $this->addArgument('css_selector', InputArgument::REQUIRED, 'CSS selector to use for extracting text');
        $this->addArgument('filename', InputArgument::REQUIRED, 'Filename to save the data to');
        $this->addOption('data_path', 'd', InputOption::VALUE_OPTIONAL, 'Path to the data files', 'var/data');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $browser = new HttpBrowser(HttpClient::create());

        $crawler = $browser->request('GET', $input->getArgument('url'));
        $content = $crawler->filter($input->getArgument('css_selector'))->each(fn($node) => $node->text());

        file_put_contents($input->getOption('data_path') . '/' . $input->getArgument('filename'), implode(' ', $content));

        return Command::SUCCESS;
    }
}