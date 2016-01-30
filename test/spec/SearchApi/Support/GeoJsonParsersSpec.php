<?php

namespace spec\SearchApi\Support;

use SearchApi;
use SearchApi\Models as Models;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * GeoJsonParsersSpec - Spec test for the GeoJsonParsers (higher level functions)
 */
class GeoJsonParsersSpec extends ObjectBehavior {
  function it_is_initializable() {
  	$this->beConstructedWith( 'Results String' );
    $this->shouldHaveType( 'SearchApi\Support\GeoJsonParsers' );
  }

	// ToDo: add in tests for the selector method

  // Reverse_Geocoder_Google_Parser tests
  function it_should_return_an_empty_array_when_passed_null() {
  	$this->beConstructedWith( null );
    $result = $this->Reverse_Geocoder_Google_Parser();
    $result->shouldBeArray();
    $result->shouldHaveCount( 0 );
  }

  function it_should_return_an_empty_array_when_passed_empty_array() {
  	$this->beConstructedWith( null );
    $result = $this->Reverse_Geocoder_Google_Parser( array() );
    $result->shouldBeArray();
    $result->shouldHaveCount( 0 );
  }

  function it_should_return_an_empty_array_when_passed_bad_formatted_array() {
  	$this->beConstructedWith( null );
    $result = $this->Reverse_Geocoder_Google_Parser( array( 'a', 'b', 'f' ) );
    $result->shouldBeArray();
    $result->shouldHaveCount( 0 );
  }

  function it_should_return_an_array_of_search_terms() {
		// Make into a mock?
    // testing string
    $google_test_string =
    '{
      "results": [
        {
  	      "address_components": [
            {
  				    "long_name": "Bedford Avenue",
  				    "short_name": "Bedford Ave",
  				    "types": [
  			  		  "route"
  				    ]
  			    },
  			    {
  				    "long_name": "Williamsburg",
  				    "short_name": "Williamsburg",
  				    "types": [
  				 		  "neighborhood",
  						  "political"
  				    ]
  			    }
  			  ],
  			  "place_id": "ChIJd8BlQ2BZwokRAFUEcm_qrcA"
  		  },
  		  {
  			  "address_components": [
  			    {
  				    "long_name": "Williamsburg",
  				    "short_name": "Williamsburg",
  				    "types": [
  						  "neighborhood",
  						  "political"
  				    ]
  			    }
  			  ],
  			  "place_id": "ChIJi27VXGBZwokRM8ErPyB91yk"
  		  }
  		],
  		"status": "OK"
  	}';
    // end of the test string

    // turning test string into json_decoder array
    $google_test_string = json_decode( $google_test_string, true );

    // checking it is array and has 2 elements
    $this->beConstructedWith( $google_test_string );
    $result = $this->Reverse_Geocoder_Google_Parser();
    $result->shouldBeArray();
    $result->shouldHaveCount( 2 );

    // checking the first element value and count
    $value_checker = $result[0];
    $value_checker->value->shouldBe( 'Bedford Avenue' );
    $value_checker->count->shouldBe( 1 );

    // checking the second element value and count
    $value_checker = $result[1];
    $value_checker->value->shouldBe( 'Williamsburg' );
    $value_checker->count->shouldBe( 2 );
  }
  // end of reverse_geocoder_parser tests

	// move to integration testing
  // combined reverse_geocoder_json_decoder and reverse_geocoder_parser tests
  function it_should_return_an_empty_array_after_json_decoding_and_parsing() {
  	$this->beConstructedWith( '{"test":"test"}' );
    $result = $this->reverse_geocoder_json_decoder( '{"test":"test"}' );
    $result = $this->Reverse_Geocoder_Google_Parser( $result );
    $result->shouldBeArray();
    $result->shouldHaveCount( 0 );
  }
  // end of combined reverse_geocoder_json_decoder and reverse_geocoder_parser tests
}