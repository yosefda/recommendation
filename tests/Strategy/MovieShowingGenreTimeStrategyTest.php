<?php

use Yosefda\Recommendation\Strategy\MovieShowingGenreTimeStrategy;
use Yosefda\Recommendation\Value\MovieShowing;

class MovieShowingGenreTimeStrategyTest extends PHPUnit\Framework\TestCase
{
    public function missingGenreDataProvider()
    {
        return [
            [["time" => "12:00"], []],
            [["genre" => "", "time" => "12:00"], []]
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required criteria 'genre'
     * @dataProvider missingGenreDataProvider
     */
    public function testGetRecommendationsMissingGenre(array $criteria, iterable $items)
    {
        $strategy = new MovieShowingGenreTimeStrategy();
        $strategy->getRecommendations($criteria, $items);
    }

    public function missingTimeDataProvider()
    {
        return [
            [["genre" => "drama"], []],
            [["genre" => "drama", "time" => ""], []]
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required criteria 'time'
     * @dataProvider missingTimeDataProvider
     */
    public function testGetRecommendationsMissingTime(array $criteria, iterable $items)
    {
        $strategy = new MovieShowingGenreTimeStrategy();
        $strategy->getRecommendations($criteria, $items);
    }

    public function getRecommendationsDataProvider()
    {
        $moonlight = new MovieShowing(
            "Moonlight",
            92,
            ["Drama", "General"],
            ["18:30:00+11:00", "20:30:00+11:00"]
        );

        $moonlight_result = new MovieShowing(
            "Moonlight",
            92,
            ["Drama", "General"],
            ["18:30:00+11:00"]
        );

        $zootopia = new MovieShowing(
            "Zootopia",
            98,
            ["Action & Adventure", "Animation", "Comedy", "General"],
            ["19:00:00+11:00", "21:00:00+11:00"]
        );

        $zootopia_result = new MovieShowing(
            "Zootopia",
            98,
            ["Action & Adventure", "Animation", "Comedy", "General"],
            ["19:00:00+11:00"]
        );

        $zootopia_result_later = new MovieShowing(
            "Zootopia",
            98,
            ["Action & Adventure", "Animation", "Comedy", "General"],
            ["21:00:00+11:00"]
        );

        $the_martian = new MovieShowing(
            "The Martian",
            92,
            ["Science Fiction & Fantasy", "General"],
            ["17:30:00+11:00", "19:30:00+11:00"]
        );

        $the_martian_result = new MovieShowing(
            "The Martian",
            92,
            ["Science Fiction & Fantasy", "General"],
            ["17:30:00+11:00"]
        );

        $shaun_the_sheep = new MovieShowing(
            "Shaun The Sheep",
            80,
            ["Animation", "Comedy", "General"],
            ["19:00:00+11:00"]
        );

        $shaun_the_sheep_result = new MovieShowing(
            "Shaun The Sheep",
            80,
            ["Animation", "Comedy", "General"],
            ["19:00:00+11:00"]
        );

        return [
            [["genre" => "drama", "time" => "12:00"], [], []], // empty items must returns empty recommendations
            [
                ["genre" => "animation", "time" => "12:00"],
                [$moonlight, $zootopia, $the_martian, $shaun_the_sheep],
                [$zootopia_result, $shaun_the_sheep_result]
            ],
            [
                ["genre" => "drama", "time" => "12:00"],
                [$moonlight, $zootopia, $the_martian, $shaun_the_sheep],
                [$moonlight_result]
            ],
            [
                ["genre" => "general", "time" => "12:00"],
                [$moonlight, $zootopia, $the_martian, $shaun_the_sheep],
                [$zootopia_result, $moonlight_result, $the_martian_result, $shaun_the_sheep_result],
            ],
            [
                ["genre" => "horror", "time" => "12:00"],
                [$moonlight, $zootopia, $the_martian, $shaun_the_sheep],
                [],
            ],
            [
                // earliest showing time already past, choose the next showing time
                ["genre" => "animation", "time" => "19:00"],
                [$moonlight, $zootopia, $the_martian, $shaun_the_sheep],
                [$zootopia_result_later],
            ],
            [
                // no more movie for the day
                ["genre" => "drama", "time" => "23:00"],
                [$moonlight, $zootopia, $the_martian, $shaun_the_sheep],
                [],
            ],
        ];
    }

    /**
     * @dataProvider getRecommendationsDataProvider
     */
    public function testGetRecommendations(array $criteria, iterable $items, iterable $expected_recommendations)
    {
        // emulate that the given input time is Australia/Sydney
        $default_timezone = date_default_timezone_get();
        date_default_timezone_set("Australia/Sydney");

        $strategy = new MovieShowingGenreTimeStrategy($criteria, $items);
        $this->assertEquals($expected_recommendations, $strategy->getRecommendations($criteria, $items));

        date_default_timezone_set($default_timezone);
    }
}