<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class content_article extends rex_console_command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('content:article');
    }

    /**
     * @throws rex_exception
     * @throws rex_api_exception
     * @throws rex_sql_exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $articleNameQuestion = new Question('Please enter an article name: ', '');
        $articleNameQuestion->setValidator(static function ($answer) {
            if (!is_string($answer) || '' === $answer) {
                throw new \RuntimeException('You must enter an article name.');
            }

            return $answer;
        });
        $articleName = $helper->ask($input, $output, $articleNameQuestion);

        $categoryQuestion = new Question('Enter a category id (optional - default: 0): ', 0);
        $categoryQuestion->setValidator(static function ($answer) {
            if (!is_numeric($answer)) {
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

        $templateQuestion = new Question('Enter a template id (optional - default: null): ', null);
        $templateQuestion->setValidator(static function ($answer) {
            if (!is_numeric($answer) && null !== $answer) {
                throw new \RuntimeException('The priority needs to be a number.');
            }

            return $answer;
        });
        $templateId = $helper->ask($input, $output, $templateQuestion);

        $articleId = content::createArticle($articleName, $categoryId, $priority, $templateId);
        $output->writeln(sprintf('Created article "%s" [%s]', $articleName, $articleId));

        return 0;
    }
}
