<?php

namespace SearchApi\Providers;

use SearchApi\Models as Models;
use SearchApi\Support as Support;
use SearchApi\Commands as Commands;
use SearchApi\Services\ReverseGeocoder as ReverseGeocoder;

/**
 * Class ReverseGeocoder - Given a latlin or place id to find a point
 * or polygon that can represent that location via GoogleMaps.
 */
class ReverseGeocoderClass implements ReverseGeocoder {
  private $Parser_Pick; // inject parser selection
  private $Url_Pick; // inject url builder selection

  public function __construct( $url_pick = null, $parser_pick = null ) {

    // selecting the url builder
    if ( $url_pick === null ) {
      // default url builder
      $url_pick = 'Google';
    }
    $this->Url_Pick = $url_pick;

    // selecting the geo parser
    if ( $parser_pick === null ) {
      $parser_pick = 'Google';
    }
    $this->Parser_Pick = $parser_pick;
  }

  public function get_Url_Pick() {
    return $this->Url_Pick;
  }

  public function get_Parser_Pick() {
    return $this->Parser_Pick;
  }

  /**
   * Function to gather the location data for the coordinates given
   *
   * @param Models\GeoCoordinate $geo_coordinate
   * @throws Exception - error in curl call
   */
  public function get_locations( Models\GeoCoordinate $geo_coordinate ) {

    // building the url
    $url_builder = new Support\GeoCoderURLBuilder( $geo_coordinate );

    // implementing a curl call using http-get curl call
    $curl_caller = new Commands\HttpGet();
    $curl_caller->setUrl( $url_builder->url_selector( $this->Url_Pick ) );
    $geocoding_results = $curl_caller->execute();

    // checking if the curl was successful

    // end of the implementing a curl call using http-get curl call

    // calling json decoder
    $geo_json_decoder = new Support\JsonDecoder();
    $decoded_json = $geo_json_decoder->reverse_geocoder_json_decoder( $geocoding_results );
    // calling parser
    $geo_parser = new Support\GeoJsonParsers( $decoded_json );
    // returns array of search terms
    return $geo_parser->parser_selector( $this->Parser_Pick );
  }
}