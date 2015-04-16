<?php
namespace Mrix\Rql\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Mrix\Rql\Command\Utils\Dumper;
use Mrix\Rql\Command\Utils\Renderer;
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
        $output->getFormatter()->setStyle('block', new OutputFormatterStyle('green', null, ['bold']));
        $output->getFormatter()->setStyle('operator', new OutputFormatterStyle('green'));
        $output->getFormatter()->setStyle('field', new OutputFormatterStyle('cyan'));

        $lexer = new Lexer();
        $tokenStream = $lexer->tokenize($input->getArgument('rql'));

        $parser = Parser::createDefault();
        $query = $parser->parse($tokenStream);

        $dumper = new Dumper();
        $renderer = new Renderer();
        $output->writeln($renderer->render($dumper->createTree($query)));
    }

    /**
     * @return OutputFormatterStyle
     */
    protected function createOperatorOutputStyle()
    {
        return new OutputFormatterStyle('green');
    }

    /**
     * @return OutputFormatterStyle
     */
    protected function createFieldOutputStyle()
    {
    }
}
