<?php

namespace Yosefda\Recommendation\Strategy;


use Yosefda\Recommendation\Value\MovieShowing;

/**
 * Class MovieShowingGenreTimeStrategy
 * @package Yosefda\Recommendation\Strategy
 *
 * Provides movie recommendations based on given genre and time.
 * Time is expected to be in 24 hours format.
 * Time can be in any timezone and will be compare to whatever timezone showing times are in. So given the same input time,
 * user in Australia/Sydney will get different recommendations to user in Australia/Perth.
 */
class MovieShowingGenreTimeStrategy extends BaseStrategy
{
    /**
     * Input criteria required by this recommendation strategy.
     */
    const CRITERIA_GENRE    = "genre";
    const CRITERIA_TIME     = "time";

    /**
     * Only movies which showing time within this interval from the input time are to be considered
     */
    const ALLOWED_SHOWING_TIME_INTERVAL = 30 * 60; // 30 minutes in secs

    /**
     * @var string[]
     * Genre and time are required for calculating recommendation.
     */
    protected $requiredInputCriteria = [
        self::CRITERIA_GENRE,
        self::CRITERIA_TIME     // 24 hours format
    ];

    /**
     * @var string
     * Genre to search on.
     */
    protected $genre;

    /**
     * @var int
     * Time to search on (in unix timestamp).
     * This will be whatever timezone user is in.
     */
    protected $time;

    /**
     * MovieShowingGenreTimeStrategy constructor.
     * @param string[] $criteria Required criteria to calculate the recommended items
     * @param MovieShowing[] $items List of items from which recommendation is to be made
     */
    public function __construct(array $criteria, iterable $items)
    {
        parent::__construct($criteria, $items);

        $this->genre = strtolower($criteria[self::CRITERIA_GENRE]);
        $this->time = $criteria[self::CRITERIA_TIME];
    }

    /**
     * Get recommendations.
     * @return MovieShowing[]
     */
    public function getRecommendations()
    {
        $recommended_items = [];

        if (empty($this->items)) {
            return $recommended_items;
        }

        // step 1. pick items that match the rules
        $input_timestamp = strtotime($this->time);
        foreach ($this->items as $item) {
            // rule #1. ignore movie which different genre
            if (!in_array($this->genre, array_map("strtolower", $item->getGenres()))) {
                continue;
            }

            // rule #2. ignore movies which showing time no later than 30 minutes after the input time
            foreach ($item->getShowings() as $showing) {
                $showing_timestamp = strtotime($showing);
                $interval_with_input_timestamp = $showing_timestamp - $input_timestamp;
                if ($interval_with_input_timestamp < self::ALLOWED_SHOWING_TIME_INTERVAL) {
                    continue;
                }
            }

            $recommended_items[] = $item;
        }

        // step 2. If more than one recommendation is returned order them by rating
        if (count($recommended_items) > 1) {
            usort($recommended_items, function($a, $b) {
                if ($a->getRating() == $b->getRating()) {
                    return 0;
                }

                return ($a->getRating() > $b->getRating()) ? -1: +1;
            });
        }

        return $recommended_items;
    }
}