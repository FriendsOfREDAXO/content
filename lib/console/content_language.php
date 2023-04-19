<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class content_language extends rex_console_command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('content:language');
    }

    /**
     * @throws rex_exception
     * @throws rex_api_exception
     * @throws rex_sql_exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $codeQuestion = new Question('Enter a language code: ', '');
        $codeQuestion->setValidator(static function ($answer) {
            if (!is_string($answer) || '' === $answer) {
                throw new \RuntimeException('You must enter a language code.');
            }

            return $answer;
        });
        $code = $helper->ask($input, $output, $codeQuestion);

        $nameQuestion = new Question('Enter a language name: ', '');
        $nameQuestion->setValidator(static function ($answer) {
            if (!is_string($answer) || '' === $answer) {
                throw new \RuntimeException('You must enter a language name.');
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $nameQuestion);

        $priorityQuestion = new Question('Enter a priority: ', '');
        $priorityQuestion->setValidator(static function ($answer) {
            if (!is_numeric($answer) || '' === $answer) {
                throw new \RuntimeException('You must enter a priority.');
            }

            return $answer;
        });
        $priority = $helper->ask($input, $output, $priorityQuestion);

        $statusQuestion = new Question('Enter a status (optional - default: false): ', false);
        $status = (bool) $helper->ask($input, $output, $statusQuestion);

        $id = content::createLanguage($code, $name, $priority, $status);
        $output->writeln(sprintf('Created language "%s" [%s]', $name, $id));

        return 0;
    }
}
