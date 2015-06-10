<?php
namespace Xiag\Rql\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Xiag\Rql\Parser\Lexer;

/**
 */
class LexerCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('rql:lexer')
            ->setDescription('RQL lexer command')
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
        $rql = $input->getArgument('rql');

        $output->getFormatter()->setStyle('token', $this->createTokenOutputStyle());

        $lexer = new Lexer();
        $tokenStream = $lexer->tokenize($rql);

        $table  = new Table($output);
        $table->setHeaders([
            'Token',
            'Type',
            'Expression',
        ]);
        while (!$tokenStream->isEnd()) {
            $token = $tokenStream->getCurrent();
            $next = $tokenStream->lookAhead();

            $table->addRow([
                'token' => $token->getValue(),
                'type'  => $token->getName(),
                'text'  => implode('', [
                    substr($rql, 0, $token->getPosition()),
                    '<token>',
                    substr($rql, $token->getPosition(), $next->getPosition() - $token->getPosition()),
                    '</token>',
                    substr($rql, $next->getPosition()),
                ]),
            ]);

            $tokenStream->next();
        }
        $table->render();
    }

    /**
     * @return OutputFormatterStyle
     */
    protected function createTokenOutputStyle()
    {
        return new OutputFormatterStyle('black', 'yellow');
    }
}
