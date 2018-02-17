<?php

namespace Yosefda\Recommendation\Parser;

/**
 * Interface IParser
 * @package Yosefda\Recommendation\Parser
 *
 * Defines common interface for parsing various data source.
 */
interface IParser
{
    /**
     * Parse the data source.
     * @return iterable
     * @throws \RuntimeException
     */
    public function parse();
}