<?php

namespace Yosefda\Recommendation\Value;

/**
 * Class MovieShowing
 * @package Yosefda\Recommendation\Value
 *
 * Value object for movie showing.
 */
class MovieShowing
{
    /**
     * @var string
     * Name of the movie.
     */
    protected $name;

    /**
     * @var int
     * Rating of the movie, 0 to 100.
     */
    protected $rating;

    /**
     * @var string[]
     * Genres of the movie in lower case.
     */
    protected $genres;

    /**
     * @var string[]
     * Showing schedules.
     */
    protected $showings;

    /**
     * MovieShowing constructor.
     * @param string $name
     * @param int $rating
     * @param string[] $genres
     * @param string[] $showings
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name, int $rating, array $genres, array $showings)
    {
        // name is mandatory
        if (empty($name)) {
            throw new \InvalidArgumentException("Missing movie name");
        }
        $this->name = $name;

        // 0 to 100 for rating
        if (!($rating >= 0 && $rating <= 100)) {
            throw new \InvalidArgumentException("Invalid value for movie rating");
        }
        $this->rating = $rating;

        $this->genres = $genres;
        $this->showings = $showings;
    }

    /**
     * Get movie name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get movie rating.
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Get movie genres.
     * @return string[]
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Get movie showing schedules.
     * @return string[]
     */
    public function getShowings()
    {
        return $this->showings;
    }
}