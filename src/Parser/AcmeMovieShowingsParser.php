<?php

namespace Yosefda\Recommendation\Parser;

use Yosefda\Recommendation\Value\MovieShowing;

/**
 * Class AcmeMovieShowingsParser
 * @package Yosefda\Recommendation\Parser
 *
 * Parser for moving showings JSON from Acme.
 */
class AcmeMovieShowingsParser implements IParser
{
    /**
     * Parse the data source.
     * @param string $json_string JSON string to parse
     * @return MovieShowing[]
     * @throws \RuntimeException
     */
    public function parse(string $json_string)
    {
        $movie_showings = [];

        // empty JSON string
        if (empty($json_string)) {
            return $movie_showings;
        }

        // decode json string
        $json_data = json_decode($json_string);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to parse JSON, reason: " . json_last_error_msg());
        }

        // nothing inside
        if (empty($json_data)) {
            return $movie_showings;
        }

        // some data inside, create collection of MovieShowing
        foreach ($json_data as $entry) {
            $movie_showings[] = new MovieShowing(
                $entry->name,
                $entry->rating,
                (array) $entry->genres,
                (array) $entry->showings
            );
        }

        return $movie_showings;
    }
}