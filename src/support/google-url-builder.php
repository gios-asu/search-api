<?php
namespace SearchApi\Support;

use SearchApi\Models as Models;

/**
 * Class URL_Builder - builds the url for google's curl call
 */
class GoogleURLBuilder {

  // keys
  private $google_key = null;
  // end of keys

  private $geo_coordinate;
  private $geo_address;

  public function __construct() {
    $this->geo_coordinate = new Models\GeoCoordinate( 0, 0 );
    $this->geo_address = '';
  }

  public function set_key( $key ) {
    $this->google_key = $key;
  }

  public function set_coords( Models\GeoCoordinate $coordinate ) {
    $this->geo_coordinate = $coordinate;
  }

  public function set_address( $address ) {
    $this->geo_address = urlencode( $address );
  }

  /**
   * Function for generating google's url for the reverse geo coder
   */
  public function reverse_google_url() {
    $service_url = 'https://maps.googleapis.com/maps/api/geocode/json?'.
    "latlng={$this->geo_coordinate->lat},{$this->geo_coordinate->lng}";

    // checking if api key is given, if so adding it to url
    if ( $this->google_key !== null ) {
      $service_url = $service_url."&key={$this->google_key}";
    }

    return $service_url;
  }

  /**
   * Function for generating google's url for the "forward" geo coder
   */
  public function forward_google_url() {
    $service_url = 'https://maps.googleapis.com/maps/api/geocode/json?'.
    "address={$this->geo_address}";

    // checking if api key is given, if so adding it to url
    if ( $this->google_key !== null ) {
      $service_url = $service_url."&key={$this->google_key}";
    }

    return $service_url;
  }
}
