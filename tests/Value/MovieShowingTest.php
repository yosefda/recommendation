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

    public function testValidObject()
    {
        $movie_showing = new MovieShowing("Zootopia", 98,
            ["Action & Adventure", "Animation", "Comedy"],
            ["19:00:00+11:00", "21:00:00+11:00"]);
        $this->assertSame("Zootopia", $movie_showing->getName());
        $this->assertSame(98, $movie_showing->getRating());

        $this->assertSame(["Action & Adventure", "Animation", "Comedy"], $movie_showing->getGenres());
        $this->assertSame(["19:00:00+11:00", "21:00:00+11:00"], $movie_showing->getShowings());
    }
}