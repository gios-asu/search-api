<?php

namespace spec\SearchApi;

use SearchApi;
use SearchApi\Models;
use SearchApi\Services\Search;
use SearchApi\Services\Tagger;
use SearchApi\Services\ReverseGeocoder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * SearchEngineSpec - Spec integration test for the search engine (higher level functions)
 */
class SearchEngineSpec extends ObjectBehavior {
  function it_is_initializable() {
    $this->shouldHaveType( 'SearchApi\SearchEngine' );
  }

  function it_should_return_an_empty_response_when_given_an_empty_request() {
    $empty_search_request = new Models\SearchRequest();
    $this->handle_request( $empty_search_request )->shouldReturnAnInstanceOf( 'SearchApi\Models\SearchResult' );
  }

  function it_should_use_tagger_if_document_provided( Search $search, Tagger $tagger, ReverseGeocoder $geocoder ) {
    $this->beConstructedWith( $search, $tagger, $geocoder );

    $tagger->tagger_service( 'phoenix' )->shouldBeCalled()->willReturn( array( new Models\Keyword( 'phoenix', 'LOCATION', null,1 ) ) );

    $foo_request = new Models\SearchRequest();
    $foo_request->document = 'phoenix';

    $response = $this->handle_request( $foo_request );
  }

  function it_should_return_a_result_when_searching_for_foo( Search $search, Tagger $tagger, ReverseGeocoder $geocoder ) {
    $this->beConstructedWith( $search, $tagger, $geocoder );
    $search->query( Argument::any() )->shouldBeCalled()->willReturn( array( new Models\SearchResultItem ) );

    $foo_request = new Models\SearchRequest();
    $foo_request->text = 'foo';

    $response = $this->handle_request( $foo_request );
    $response->results->shouldHaveCount( 1 );
  }
}
