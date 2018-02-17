<?php

use Yosefda\Recommendation\Fetcher\GuzzleHttpFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class GuzzleHttpFetcherTest extends PHPUnit\Framework\TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage HTTP status code 404 returned
     */
    public function testFetchNon200StatusCode()
    {
        $mock_response = $this->getMockBuilder(Response::class)
            ->setMethods(["getStatusCode"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_response->expects($this->once())
            ->method("getStatusCode")
            ->will($this->returnValue(404));

        $mock_guzzle = $this->getMockBuilder(Client::class)
            ->setMethods(["get"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_guzzle->expects($this->once())
            ->method("get")
            ->will($this->returnValue($mock_response));

        $fetcher = new GuzzleHttpFetcher($mock_guzzle);
        $fetcher->fetch("http://somewhere.at/hello");
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Something went wrong
     */
    public function testFetchGuzzleException()
    {
        $mock_request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock_guzzle = $this->getMockBuilder(Client::class)
            ->setMethods(["get"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_guzzle->expects($this->once())
            ->method("get")
            ->will($this->throwException(new RequestException("Something went wrong", $mock_request)));

        $fetcher = new GuzzleHttpFetcher($mock_guzzle);
        $fetcher->fetch("http://somewhere.at/hello");
    }

    public function testFetchSuccess()
    {
        $json_response = file_get_contents(dirname(dirname(__FILE__)) . "/Fixtures/acme_movie_showings.json");

        $mock_response = $this->getMockBuilder(Response::class)
            ->setMethods(["getStatusCode", "getBody"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_response->expects($this->once())
            ->method("getStatusCode")
            ->will($this->returnValue(200));
        $mock_response->expects($this->once())
            ->method("getBody")
            ->will($this->returnValue($json_response));

        $mock_guzzle = $this->getMockBuilder(Client::class)
            ->setMethods(["get"])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_guzzle->expects($this->once())
            ->method("get")
            ->will($this->returnValue($mock_response));

        $fetcher = new GuzzleHttpFetcher($mock_guzzle);
        $this->assertSame($json_response, $fetcher->fetch("http://somewhere.at/hello"));
    }
}