<?php
declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;

require_once __DIR__ . '/vendor/autoload.php';

$container = new ContainerBuilder();
$container
    ->register('lexer', \Parentheses\Lexer::class)
    ->setPublic(true)
;
$container
    ->register('validator', \Parentheses\Validator::class)
    ->addArgument(new \Symfony\Component\DependencyInjection\Reference('lexer'))
    ->setPublic(true)
;
$container
    ->register('validate_command', \App\Command\ValidateParentheses::class)
    ->addArgument(null)
    ->addArgument(new \Symfony\Component\DependencyInjection\Reference('service_container'))
    ->setPublic(true)
;

$container->compile();

$application = new Application();
$application->add($container->get('validate_command'));
$application->run();