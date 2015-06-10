<?php
namespace Xiag\Rql\Command\Utils;

/**
 * @link https://github.com/liushuping/ascii-tree
 */
class Renderer
{
    const C0 = "\xe2\x94\x9c";
    const C1 = "\xe2\x94\x80";
    const C2 = "\xe2\x94\x94";
    const C3 = "\xe2\x94\x82";

    /**
     * @var array
     */
    private $levels = [];

    /**
     * @param array $tree
     * @return string
     */
    public function render(array $tree)
    {
        $this->levels = [];
        return $this->generate($tree, true);
    }

    /**
     * @param array $tree
     * @param bool $isEnd
     * @return string
     */
    protected function compose(array $tree, $isEnd)
    {
        $result = "\r\n";

        if ($tree['level'] === 0) {
            return $tree['value'];
        }

        for ($i = 1; $i < $tree['level']; ++$i) {
            $result .= $this->levels[$i] ? ' ' : self::C3;
            $result .= '  ';
        }

        return $result . ($isEnd ? self::C2 : self::C0) . self::C1 . ' ' . $tree['value'];
    }

    /**
     * @param array $tree
     * @param bool $isEnd
     * @return string
     */
    protected function generate(array $tree, $isEnd)
    {
        $result = $this->compose($tree, $isEnd);

        if (!empty($tree['nodes'])) {
            $last = count($tree['nodes']) - 1;
            foreach ($tree['nodes'] as $index => $subtree) {
                $this->levels[$subtree['level']] = $index === $last;
                $result .=  $this->generate($subtree, $index === $last);
            }
        }

        return $result;
    }
}
