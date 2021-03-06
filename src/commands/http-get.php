<?php

namespace SearchApi\Commands;
use SearchApi\Commands;
use Exception;

/**
 * Command for making simple GET requests.
 *
 * @var CurlObject $curl object returned by curl_init()
 * @var string $url Url to make a GET request to
 */
class HttpGet implements Command {
  private $curl;
  private $url;
  private $timeout_seconds = 120;

  function __construct() {
    // create curl resource
    $this->curl = curl_init();
  }

  function __destruct() {
    curl_close( $this->curl );
  }

  /**
   * Set the Url to make a GET request to
   *
   * @var string $url Url to make a GET request to
   */
  function setUrl( $url ) {
    $this->url = $url;
  }

  /**
   * Execute command - simple GET request to Url set by setUrl
   */
  function execute() {
    // Use curl to make GET request to SOLR server.
    $ch = $this->curl;
    curl_setopt( $ch, CURLOPT_URL, $this->url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $this->timeout_seconds );
    $response = curl_exec( $ch );

    // checking if curl failed
    if ( $response === false ) {
      $this->curl_fail( $ch );
    }

    return $response;
  }

  /**
   * Throw Exception - if curl fails, get infomation and throw an exception
   *
   * @throws Exception - on bad curl execution: returns curl info
   */
  function curl_fail( $ch ) {
    $info = curl_getinfo( $ch );
    $excep_string = "\n" . http_build_query( $info, '', "\n" );
    throw new Exception( 'error occured during curl exec. Additioanl info: ' . $excep_string );
  }
}
