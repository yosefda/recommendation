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
     * Get recommended items.
     * @param string[] $criteria Required criteria to calculate the recommended items
     * @param iterable $items List of items from which recommendation is to be made
     * @return iterable
     * @throws \InvalidArgumentException
     */
    public function getRecommendations(array $criteria, iterable $items)
    {
        parent::getRecommendations($criteria, $items);

        $this->genre = strtolower($criteria[self::CRITERIA_GENRE]);
        $this->time = $criteria[self::CRITERIA_TIME];

        $recommended_items = [];

        if (empty($this->items)) {
            return $recommended_items;
        }

        $input_timestamp = strtotime($this->time);

        // step 1. pick items that match the rules
        foreach ($this->items as $item) {
            // rule #1. ignore movie which different genre
            if (!in_array($this->genre, array_map("strtolower", $item->getGenres()))) {
                continue;
            }

            // rule #2. ignore movies which showing time no later than 30 minutes after the input time
            $valid_showing_time = false;
            foreach ($item->getShowings() as $showing) {
                $showing_timestamp = strtotime($showing);
                $interval_with_input_timestamp = $showing_timestamp - $input_timestamp;
                if ($interval_with_input_timestamp >= self::ALLOWED_SHOWING_TIME_INTERVAL) {
                    $valid_showing_time = true;
                }
            }

            if ($valid_showing_time) {
                $recommended_items[] = $item;
            }
        }

        // step 2. rebuild recommended items by picking the closest next showing time
        $temp_recommendend_items = $recommended_items;
        $recommended_items = [];
        foreach ($temp_recommendend_items as $item) {
            $closest_next_showing = "";
            $showings = $item->getShowings();

            if (count($showings) === 1) {
                // only has 1 showing time, use the showing time if it's later than the input time
                if (strtotime($showings[0]) - $input_timestamp > 0) {
                    $closest_next_showing = $showings[0];
                }
            } else {
                foreach ($item->getShowings() as $showing) {
                    if (empty($closest_next_showing)) {
                        // 1st showing in the list
                        $closest_next_showing = $showing;
                        continue;
                    }

                    // use the current showing if the previous closest diff <= 0 (already past)
                    // current showing is closest to the given input time
                    $closest_diff = strtotime($closest_next_showing) - $input_timestamp;
                    $current_diff = strtotime($showing) - $input_timestamp;
                    if ($closest_diff <= 0 || (($current_diff <= $closest_diff))) {
                        $closest_next_showing = $showing;
                    }
                }
            }

            // only recommend movies that are still showing
            if (!empty($closest_next_showing)) {
                $recommended_items[] = new MovieShowing(
                    $item->getName(),
                    $item->getRating(),
                    $item->getGenres(),
                    [$closest_next_showing]
                );
            }

        }


        // step 3. If more than one recommendation is returned order them by rating
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