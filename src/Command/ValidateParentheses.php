<?php
declare(strict_types=1);

namespace App\Command;

use Parentheses\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ValidateParentheses extends Command
{
    private $container;

    public function __construct($name = null, ContainerInterface $container)
    {
        parent::__construct($name);
        $this->container = $container;
    }

    public function configure()
    {
        $this->setName('app:validate');
        $this->addArgument('file', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        if (!is_file($file) || !is_readable($file)) {
            $output->writeln('No such file');

            return;
        }

        /** @var Validator $validator */
        $validator = $this->container->get('validator');
        try {
            $result = $validator->validate(file_get_contents($file));
            $output->writeln('File is ' . ($result ? 'valid' : 'not valid'));
        } catch (\InvalidArgumentException $e) {
            // It's better to use translation component
            $output->writeln(
                preg_replace(
                    '/Unexpected symbol (.) at position ([\d]+)/',
                    'Ошибочный символ "$1", позиция $2',
                    $e->getMessage()
                )
            );
        }
    }
}
