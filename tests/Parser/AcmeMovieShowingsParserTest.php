<?php

use Yosefda\Recommendation\Parser\AcmeMovieShowingsParser;
use Yosefda\Recommendation\Value\MovieShowing;

class AcmeMovieShowingsParserTest extends PHPUnit\Framework\TestCase
{
    public function parsingDataSource()
    {
        $json_string = file_get_contents(dirname(dirname(__FILE__)) . "/Fixtures/acme_movie_showings.json");

        return [
            ["", []], // empty JSON string
            ["[]", []], // empty JSON array
            [
                $json_string,
                [
                    new MovieShowing(
                        "Moonlight",
                        98,
                        ["Drama"],
                        ["18:30:00+11:00", "20:30:00+11:00"]
                    )
                ]
            ]
        ];
    }

    /**
     * @dataProvider parsingDataSource
     */
    public function testParse(string $json_string, iterable $expected_output)
    {
        $parser = new AcmeMovieShowingsParser();
        $this->assertEquals($expected_output, $parser->parse($json_string));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Failed to parse JSON, reason:
     */
    public function testParseBrokenJSON()
    {
        $json_string = file_get_contents(dirname(dirname(__FILE__)) . "/Fixtures/broken_acme_movie_showings.json");

        $parser = new AcmeMovieShowingsParser();
        $parser->parse($json_string);
    }
}