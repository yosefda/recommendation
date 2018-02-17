<?php

namespace Yosefda\Recommendation\Runner;
use Yosefda\Recommendation\Fetcher\IFetcher;
use Yosefda\Recommendation\Parser\IParser;
use Yosefda\Recommendation\Strategy\BaseStrategy;

/**
 * Class Main
 * @package Yosefda\Recommendation\Runner
 *
 * Runner for the Genre Time Recommendation.
 */
class Main
{
    /**
     * @var string[]
     * List of criteria to calculate recommendations.
     */
    protected $criteria;

    /**
     * @var string
     * Data source URI.
     */
    protected $dataSourceUri;

    /**
     * @var IFetcher
     * Fetcher used to fetch data source.
     */
    protected $fetcher;

    /**
     * @var IParser
     * Parser used to parse data source.
     */
    protected $parser;

    /**
     * @var BaseStrategy
     * Strategy/algorithm used to calculate recommendations.
     */
    protected $strategy;

    /**
     * Main constructor.
     * @param array $criteria
     * @param string $data_source_uri
     * @param IFetcher $fetcher
     * @param IParser $parser
     * @param BaseStrategy $strategy
     */
    public function __construct(
        array $criteria,
        string $data_source_uri,
        IFetcher $fetcher,
        IParser $parser,
        BaseStrategy $strategy
    )
    {
        $this->criteria         = $criteria;
        $this->dataSourceUri    = $data_source_uri;
        $this->fetcher          = $fetcher;
        $this->parser           = $parser;
        $this->strategy         = $strategy;
    }

    /**
     * Run the application.
     * @return string Recommendation output
     */
    public function run()
    {
        $recommended_movies = [];

        try {

            // step 1. fetch data source JSON
            $data_source_json_string = $this->fetcher->fetch($this->dataSourceUri);

            // step 2. parse the JSON
            $movie_showings = $this->parser->parse($data_source_json_string);

            // step 3. get recommendations
            $recommended_movies = $this->strategy->getRecommendations($this->criteria, $movie_showings);

        } catch (\Exception $ex) {
            return "Application error: {$ex->getMessage()}";
        }

        return $this->formatOutput($recommended_movies);
    }

    /**
     * Format output.
     * @param iterable $recommended_movies
     * @return string
     */
    protected function formatOutput(iterable $recommended_movies)
    {
        if (empty($recommended_movies)) {
            return "no movie recommendations";
        }

        // print movie name and the closest showing time
        $output = [];
        foreach ($recommended_movies as $movie) {
            $showings = $movie->getShowings();
            $showing_time = date("g:ia", strtotime($showings[0]));
            $output[] = "{$movie->getName()}, showing at {$showing_time}";
        }

        return implode(PHP_EOL, $output);
    }
}