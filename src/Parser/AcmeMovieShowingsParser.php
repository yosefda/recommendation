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
     * @var string
     * JSON string of the moving showings data.
     */
    protected $json_string;

    /**
     * AcmeMovieShowingsParser constructor.
     * @param string $json_string
     */
    public function __construct(string $json_string)
    {
        $this->json_string = $json_string;
    }

    /**
     * Parse the data source.
     * @return MovieShowing[]
     * @throws \RuntimeException
     */
    public function parse()
    {
        $movie_showings = [];

        // empty JSON string
        if (empty($this->json_string)) {
            return $movie_showings;
        }

        // decode json string
        $json_data = json_decode($this->json_string);
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