<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class content_category extends rex_console_command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('content:category');
    }

    /**
     * @throws rex_exception
     * @throws rex_api_exception
     * @throws rex_sql_exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $nameQuestion = new Question('Please enter a category name: ', '');
        $nameQuestion->setValidator(static function ($answer) {
            if (!is_string($answer) || '' === $answer) {
                throw new \RuntimeException('You must enter a category name.');
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $nameQuestion);

        $categoryQuestion = new Question('Enter a category id (optional - default: ""): ', '');
        $categoryQuestion->setValidator(static function ($answer) {
            if (!is_numeric($answer) && '' !== $answer) {
                throw new \RuntimeException('The category id needs to be a number.');
            }

            return $answer;
        });
        $categoryId = $helper->ask($input, $output, $categoryQuestion);

        $priorityQuestion = new Question('Enter a priority (optional - default: -1): ', -1);
        $priorityQuestion->setValidator(static function ($answer) {
            if (!is_numeric($answer)) {
                throw new \RuntimeException('The priority needs to be a number.');
            }

            return $answer;
        });
        $priority = $helper->ask($input, $output, $priorityQuestion);

        $statusQuestion = new Question('Enter a status (optional - default: null): ', null);
        $statusQuestion->setValidator(static function ($answer) {
            if (!is_numeric($answer) && null !== $answer) {
                throw new \RuntimeException('The status needs to be a number.');
            }

            return $answer;
        });
        $status = $helper->ask($input, $output, $statusQuestion);

        $id = content::createCategory($name, $categoryId, $priority, $status);
        $output->writeln(sprintf('Created category "%s" [%s]', $name, $id));

        return 0;
    }
}
