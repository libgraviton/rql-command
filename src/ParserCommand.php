<?php
namespace Xiag\Rql\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Xiag\Rql\Command\Utils\Dumper;
use Xiag\Rql\Command\Utils\Renderer;
use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser;
use Xiag\Rql\Parser\TokenParser;

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
        $output->getFormatter()->setStyle('block', $this->getBlockOutputStyle());
        $output->getFormatter()->setStyle('operator', $this->getOperatorOutputStyle());
        $output->getFormatter()->setStyle('field', $this->getFieldOutputStyle());

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
    protected function getBlockOutputStyle()
    {
        return new OutputFormatterStyle('green', null, ['bold']);
    }

    /**
     * @return OutputFormatterStyle
     */
    protected function getOperatorOutputStyle()
    {
        return new OutputFormatterStyle('green');
    }

    /**
     * @return OutputFormatterStyle
     */
    protected function getFieldOutputStyle()
    {
        return new OutputFormatterStyle('cyan');
    }
}
