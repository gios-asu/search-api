<?php

namespace spec\SearchApi\Providers;

use SearchApi;
use SearchApi\Models as Models;
use SearchApi\Providers as Providers;
use SearchApi\Support as Support;
use SearchApi\Commands as Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * ReverseGeocoderClassSpec - Spec integration test for the ReverseGeocoderClass (higher level functions)
 */
class GoogleReverseGeocoderSpec extends ObjectBehavior {
  private $geo_response = '{"results": [{"address_components": 
			[{"long_name": "Bedford Avenue","short_name": "Bedford Ave","types": 
			["route"]},{"long_name": "Williamsburg","short_name": "Williamsburg","types": 
			["neighborhood","political"]}],"place_id": "ChIJd8BlQ2BZwokRAFUEcm_qrcA"},
			{"address_components": [{"long_name": "Williamsburg","short_name": 
			"Williamsburg","types": ["neighborhood","political"]}],"place_id": 
			"ChIJi27VXGBZwokRM8ErPyB91yk"}],"status": "OK"}';

  private $geo_coord_response = '{"results": [{"formatted_address": "Arizona, USA",
      "geometry": {"bounds": {"northeast": {"lat": 37.0042599,"lng": -109.0452231
      },"southwest": {"lat": 31.3321771,"lng": -114.8165909}},"location": {
      "lat": 34.0489281,"lng": -111.0937311},"location_type": "APPROXIMATE",
      "viewport": {"northeast": {"lat": 37.0042599,"lng": -109.0452231},"southwest": {
      "lat": 31.3321771,"lng": -114.8165909}}},"place_id": "ChIJaxhMy-sIK4cRcc3Bf7EnOUI"
      }],"status": "OK"}';

  function it_is_initializable() {
    $this->shouldHaveType( 'SearchApi\Providers\GoogleReverseGeocoder' );
  }

  // reverse geo coding tests
  function it_should_return_a_result_for_a_coord( Support\GoogleURLBuilder $url_builder,
    Commands\HttpGet $http_get_command,
    Support\JsonDecoder $geo_json_decoder,
    Support\GoogleReverseGeocoderParser $geo_parser ) {

    // setting up the class variables
    $geo_coordinate = new Models\GeoCoordinate( 40.714224, -73.961452 );
    $this->beConstructedWith( $url_builder, $http_get_command, $geo_json_decoder, $geo_parser );

    // setting up predictions
    // url_builder predictions
    $url_builder->set_coords( Argument::type( 'SearchApi\Models\GeoCoordinate' ) )->shouldBeCalled();
    $url_builder->reverse_google_url()->shouldBeCalled()->willReturn( 'https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452' );
    // curl call predictions
    $http_get_command->setUrl( Argument::type( 'string' ) )->shouldBeCalled();
    $http_get_command->execute()->shouldBeCalled()->willReturn( $this->geo_response );
    // decoder predictions
    $geo_json_decoder->reverse_geocoder_json_decoder( Argument::type( 'string' ) )->shouldBeCalled()
    ->willReturn( json_decode( $this->geo_response, true ) );
    // parser predictions
    $geo_parser->google_reverse_geocoder_parser( Argument::type( 'Array' ) )->shouldBeCalled();

    // calling the function
    $result = $this->get_locations( $geo_coordinate );
  }

  function it_should_return_a_url_for_reverse_google() {
    $geo_coordinate = new Models\GeoCoordinate( 40.714224, -73.961452 );
    $this->get_reverse_url( $geo_coordinate )
    ->shouldBe( 'https://maps.googleapis.com/maps/api/geocode/json?'.
    "latlng={$geo_coordinate->lat},{$geo_coordinate->lng}" );
  }
  // end of reverse geo coder tests

  // forward geo coding tests
  function it_should_return_a_result_for_a_address( Support\GoogleURLBuilder $url_builder,
    Commands\HttpGet $http_get_command,
    Support\JsonDecoder $geo_json_decoder,
    Support\GoogleForwardGeocoderParser $geo_parser ) {

    // setting up the class variables
    $address = 'Arizona';
    $this->beConstructedWith( $url_builder, $http_get_command, $geo_json_decoder, null, $geo_parser );

    // setting up predictions
    // url_builder predictions
    $url_builder->set_address( Argument::type( 'string' ) )->shouldBeCalled();
    $url_builder->forward_google_url()->shouldBeCalled()->willReturn( 'https://maps.googleapis.com/maps/api/geocode/json?address=Arizona' );
    // curl call predictions
    $http_get_command->setUrl( Argument::type( 'string' ) )->shouldBeCalled();
    $http_get_command->execute()->shouldBeCalled()->willReturn( $this->geo_coord_response );
    // decoder predictions
    $geo_json_decoder->reverse_geocoder_json_decoder( Argument::type( 'string' ) )->shouldBeCalled()
    ->willReturn( json_decode( $this->geo_coord_response, true ) );
    // parser predictions
    $geo_parser->google_forward_geocoder_parser( Argument::type( 'Array' ) )->shouldBeCalled();

    // calling the function
    $result = $this->get_coordinates( $address );
  }

  function it_should_return_a_url_for_forward_google() {
    $address = 'Arizona';
    $this->get_forward_url( $address )
    ->shouldBe( 'https://maps.googleapis.com/maps/api/geocode/json?'.
    "address={$address}" );
  }
  // end of the forward geo coding tests
}
