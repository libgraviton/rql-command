<?php
namespace Xiag\Rql\Command\Utils;

use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\Node;
use Xiag\Rql\Parser\DataType\Glob;

/**
 * @link https://github.com/liushuping/freetree
 */
class Dumper
{
    /**
     * @param Query $query
     * @return array
     */
    public function createTree(Query $query)
    {
        $nodes = [];
        if ($query->getSelect() !== null) {
            $nodes[] = $this->processSelect($query->getSelect(), 1);
        }
        if ($query->getQuery() !== null) {
            $nodes[] = $this->processQuery($query->getQuery(), 1);
        }
        if ($query->getSort() !== null) {
            $nodes[] = $this->processSort($query->getSort(), 1);
        }
        if ($query->getLimit() !== null) {
            $nodes[] = $this->processLimit($query->getLimit(), 1);
        }

        return $this->createNode(0, '<block>query</block>', $nodes);
    }

    protected function processSelect(Node\SelectNode $select, $level = 0)
    {
        return $this->createNode(
            $level,
            sprintf('<block>%s</block>', $select->getNodeName()),
            array_values(array_map(function ($field) use ($level) {
                return $this->createNode($level + 1, sprintf('<field>%s</field>', $field));
            }, $select->getFields()))
        );
    }

    protected function processSort(Node\SortNode $sort, $level = 0)
    {
        $nodes = [];
        foreach ($sort->getFields() as $field => $direction) {
            $nodes[] = $this->createNode(
                $level + 1,
                sprintf(
                    '<field>%s</field> %s',
                    $field,
                    $direction === Node\SortNode::SORT_DESC ? "\xe2\x96\xbe" : "\xe2\x96\xb4"
                )
            );
        }

        return $this->createNode(
            $level,
            sprintf('<block>%s</block>', $sort->getNodeName()),
            $nodes
        );
    }

    protected function processLimit(Node\LimitNode $limit, $level = 0)
    {
        $nodes = [
            $this->createNode($level + 1 , sprintf('limit: %d', $limit->getLimit())),
        ];
        if ($limit->getOffset() !== null) {
            $nodes[] = $this->createNode($level + 1 , sprintf('offset: %d', $limit->getOffset()));
        }

        return $this->createNode(
            $level,
            sprintf('<block>%s</block>', $limit->getNodeName()),
            $nodes
        );
    }

    protected function processQuery(Node\AbstractQueryNode $query, $level = 0)
    {
        return $this->createNode(
            $level,
            '<block>query</block>',
            [$this->processOperator($query, $level + 1)]
        );
    }

    protected function processOperator(Node\AbstractQueryNode $query, $level = 0)
    {
        if ($query instanceof Node\Query\AbstractArrayOperatorNode) {
            return $this->processArrayOperator($query, $level);
        } elseif ($query instanceof Node\Query\AbstractScalarOperatorNode) {
            return $this->processScalarOperator($query, $level);
        } elseif ($query instanceof Node\Query\AbstractLogicOperatorNode) {
            return $this->processLogicOperator($query, $level);
        }

        throw new \InvalidArgumentException(sprintf('Unknown node type "%s"', get_class($query)));
    }

    protected function processArrayOperator(Node\Query\AbstractArrayOperatorNode $node, $level = 0)
    {
        return $this->createNode(
            $level,
            sprintf('<operator>%s</operator>', $node->getNodeName($node)),
            [
                $this->createNode($level + 1, sprintf('<field>%s</field>', $node->getField())),
                $this->createNode(
                    $level + 1,
                    '',
                    array_map(function ($value) use ($level) {
                        return $this->createNode($level + 2, $this->dumpValue($value));
                    }, $node->getValues())
                ),
            ]
        );
    }

    protected function processScalarOperator(Node\Query\AbstractScalarOperatorNode $node, $level = 0)
    {
        return $this->createNode(
            $level,
            sprintf('<operator>%s</operator>', $node->getNodeName($node)),
            [
                $this->createNode($level + 1, sprintf('<field>%s</field>', $node->getField())),
                $this->createNode($level + 1, $this->dumpValue($node->getValue())),
            ]
        );
    }

    protected function processLogicOperator(Node\Query\AbstractLogicOperatorNode $node, $level = 0)
    {
        return $this->createNode(
            $level,
            sprintf('<operator>%s</operator>', $node->getNodeName($node)),
            array_map(function (Node\AbstractQueryNode $query) use ($level) {
                return $this->processOperator($query, $level + 1);
            }, $node->getQueries())
        );
    }

    protected function createNode($level, $value, array $children = [])
    {
        return [
            'level' => $level,
            'value' => $value,
            'nodes' => $children,
        ];
    }

    protected function dumpValue($value)
    {
        if ($value === null) {
            return 'null';
        } elseif ($value === true) {
            return 'true';
        } elseif ($value === false) {
            return 'false';
        } elseif (is_int($value) || is_float($value)) {
            return $this->dumpNumber($value);
        } elseif (is_string($value)) {
            return sprintf('"%s"', addcslashes($value, "\0\t\"\$\\"));
        } elseif ($value instanceof \DateTimeInterface) {
            return $value->format('c');
        } elseif ($value instanceof Glob) {
            return $value->toRegex();
        } elseif (is_array($value)) {
            return '[' . implode(', ', array_map([$this, 'dumpValue'], $value)) . ']';
        } else {
            return (string)$value;
        }
    }

    protected function dumpNumber($number)
    {
        if (($locale = setlocale(LC_NUMERIC, 0)) !== false) {
            setlocale(LC_NUMERIC, 'C');
        }

        $result = (string)$number;
        if (is_float($number) && ctype_digit($result) && strpos($result, '.') === false) {
            $result = $result . '.0';
        }

        if ($locale !== false) {
            setlocale(LC_NUMERIC, $locale);
        }

        return $result;
    }
}
