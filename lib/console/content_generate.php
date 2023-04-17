<?php

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class content_generate extends rex_console_command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('content:generate')
            ->setDescription('Generate content from a php file')
            ->addArgument('filepath', InputArgument::REQUIRED, 'Filepath to generate content from');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = rex_path::backend($input->getArgument('filepath'));

        if (!file_exists($filePath)) {
            throw new InvalidArgumentException(sprintf('File "%s" not found!', $filePath));
        }

        if ('php' !== pathinfo($filePath, PATHINFO_EXTENSION)) {
            throw new InvalidArgumentException(sprintf('File "%s" is not a php file!', $filePath));
        }

        require_once $filePath;

        return 0;
    }
}
