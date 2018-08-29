<?php

namespace Swoft\DataParser;

/**
 * Class DataParserAwareTrait
 * @package Swoft\DataParser
 * @author inhere <in.798@qq.com>
 */
trait DataParserAwareTrait
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @return ParserInterface
     */
    public function getParser(): ParserInterface
    {
        if (!$this->parser) {
            $this->parser = new PhpParser();
        }

        return $this->parser;
    }

    /**
     * @param ParserInterface $parser
     */
    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;
    }
}
