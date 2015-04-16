<?php
namespace Mrix\Rql\Command\Utils;

use Mrix\Rql\Parser\Query;
use Mrix\Rql\Parser\Node;

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
            '<block>select</block>',
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
            '<block>sort</block>',
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
            '<block>limit</block>',
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
            sprintf('<operator>%s</operator>', $this->getQueryNodeName($node)),
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
            sprintf('<operator>%s</operator>', $this->getQueryNodeName($node)),
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
            sprintf('<operator>%s</operator>', $this->getQueryNodeName($node)),
            array_map(function (Node\AbstractQueryNode $query) use ($level) {
                return $this->processOperator($query, $level + 1);
            }, $node->getQueries())
        );
    }

    protected function getQueryNodeName(Node\AbstractQueryNode $node)
    {
        $class = get_class($node);
        if (preg_match('/\\\\(\w+)node$/i', $class, $matches)) {
            return strtolower($matches[1]);
        } else {
            return $class;
        }
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
        return var_export($value, true);
    }
}