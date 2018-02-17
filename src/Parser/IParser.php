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
     * @param string $json_string JSON string to parse
     * @return iterable
     * @throws \RuntimeException
     */
    public function parse(string $json_string);
}