<?php

namespace Yosefda\Recommendation\Fetcher;

/**
 * Interface IFetcher
 * @package Yosefda\Recommendation\Fetcher
 *
 * Defines common interface for fetching data source.
 */
interface IFetcher
{
    /**
     * Fetch the data source.
     * @param string $uri URI of the data source
     * @return string Content of the data source
     */
    public function fetch(string $uri);
}