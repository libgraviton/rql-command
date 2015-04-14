<?php
namespace Mrix\Rql\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Mrix\Rql\Parser\Lexer;
use Mrix\Rql\Parser\Parser;
use Mrix\Rql\Parser\TokenParser;

/**
 */
class ParserCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('rql:parser')
            ->setDescription('RQL parser command')
            ->addArgument(
                'rql',
                InputArgument::REQUIRED,
                'RQL query'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lexer = new Lexer();
        $tokenStream = $lexer->tokenize($input->getArgument('rql'));

        $parser = Parser::createDefault();

        $dumper = new CliDumper();
        $cloner = new VarCloner();
        $dumper->dump($cloner->cloneVar($parser->parse($tokenStream)), function ($line, $depth) use ($output) {
            if ($depth >= 0) {
                $output->writeln(str_repeat('  ', $depth) . $line);
            }
        });
    }
}
