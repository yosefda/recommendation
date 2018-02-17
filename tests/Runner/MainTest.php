<?php

use Yosefda\Recommendation\Runner\Main;
use Yosefda\Recommendation\Fetcher\IFetcher;
use Yosefda\Recommendation\Parser\IParser;
use Yosefda\Recommendation\Strategy\BaseStrategy;
use Yosefda\Recommendation\Strategy\MovieShowingGenreTimeStrategy;
use Yosefda\Recommendation\Parser\AcmeMovieShowingsParser;
use Yosefda\Recommendation\Fetcher\GuzzleHttpFetcher;
use Yosefda\Recommendation\Value\MovieShowing;

class MainTest extends PHPUnit\Framework\TestCase
{
    public function runDataProvider()
    {
        $mock_fetcher = $this->getMockBuilder(GuzzleHttpFetcher::class)
            ->setMethods(["fetch"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_fetcher->expects($this->any())
            ->method("fetch")
            ->will($this->returnValue(""));

        $mock_parser = $this->getMockBuilder(AcmeMovieShowingsParser::class)
            ->setMethods(["parse"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_parser->expects($this->any())
            ->method("parse")
            ->will($this->returnValue([]));

        $mock_strategy_empty = $this->getMockBuilder(MovieShowingGenreTimeStrategy::class)
            ->setMethods(["getRecommendations"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_strategy_empty->expects($this->once())
            ->method("getRecommendations")
            ->will($this->returnValue([]));

        $mock_strategy_some = $this->getMockBuilder(MovieShowingGenreTimeStrategy::class)
            ->setMethods(["getRecommendations"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_strategy_some->expects($this->once())
            ->method("getRecommendations")
            ->will($this->returnValue([new MovieShowing("Some movie", 90, ["animation"], ["19:00:00+11:00"])]));

        return [
            // empty recommendation
            [
                ["genre" => "animation", "time" => "12:00"],
                "http://somewhere.at/hello",
                $mock_fetcher,
                $mock_parser,
                $mock_strategy_empty,
                "no movie recommendations"
            ],
            // some recommendation
            [
                ["genre" => "animation", "time" => "12:00"],
                "http://somewhere.at/hello",
                $mock_fetcher,
                $mock_parser,
                $mock_strategy_some,
                "Some movie, showing at 7:00pm"
            ],
        ];
    }


    /**
     * @dataProvider runDataProvider
     */
    public function testRun(
        array $criteria,
        string $data_source_uri,
        IFetcher $fetcher,
        IParser $parser,
        BaseStrategy $strategy,
        string $expected_output
    )
    {
        // emulate that the given input time is Australia/Sydney
        $default_timezone = date_default_timezone_get();
        date_default_timezone_set("Australia/Sydney");

        $runner = new Main($criteria, $data_source_uri, $fetcher, $parser, $strategy);
        $this->assertEquals($expected_output, $runner->run());

        date_default_timezone_set($default_timezone);
    }

    public function testRunError()
    {
        $criteria = ["genre" => "animation", "time" => "12:00"];
        $uri =  "http://somewhere.at/hello";

        $mock_fetcher = $this->getMockBuilder(GuzzleHttpFetcher::class)
            ->setMethods(["fetch"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_fetcher->expects($this->any())
            ->method("fetch")
            ->will($this->throwException(new RuntimeException("something went wrong")));

        $mock_parser = $this->getMockBuilder(AcmeMovieShowingsParser::class)
            ->setMethods(["parse"])
            ->disableOriginalConstructor()
            ->getMock();

        $mock_strategy = $this->getMockBuilder(MovieShowingGenreTimeStrategy::class)
            ->setMethods(["getRecommendations"])
            ->disableOriginalConstructor()
            ->getMock();

        $runner = new Main($criteria, $uri, $mock_fetcher, $mock_parser, $mock_strategy);
        $this->assertSame("Application error: something went wrong", $runner->run());
    }
}