<?php

use Yosefda\Recommendation\Value\MovieShowing;

class MovieShowingTest extends PHPUnit\Framework\TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing movie name
     */
    public function testConstructMissingMovieName()
    {
        $movie_showing = new MovieShowing("", 10, [], []);
    }

    public function invalidMovieRatingDataProvider()
    {
        return [
            ["Some Movie 1", -1, [], []],
            ["Some Movie 2", 101, [],[]]
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid value for movie rating
     * @dataProvider invalidMovieRatingDataProvider
     */
    public function testConstructInvalidMovieRating(string $name, int $rating, array $genres, array $showings)
    {
        $movie_showing = new MovieShowing($name, $rating, $genres, $showings);
    }


    public function validObjectDataProvider()
    {
        return [
            ["Some Movie 1", 75, ["Action", "Comedy"], [1234567890, 234567890]],
            ["Some Movie 2", 85, ["thriller", "Horror"], [1234567890]]
        ];
    }

    /**
     * @dataProvider validObjectDataProvider
     */
    public function testValidObject(string $name, int $rating, array $genres, array $showings)
    {
        $movie_showing = new MovieShowing($name, $rating, $genres, $showings);
        $this->assertSame($name, $movie_showing->getName());
        $this->assertSame($rating, $movie_showing->getRating());

        $expected_genres = array_map("strtolower", $genres);
        $this->assertSame($expected_genres, $movie_showing->getGenres());
    }
}