<?php

namespace SearchApi\Providers;

use SearchApi\Services\TextMapper;
use SearchApi\Models\Synonym;

/**
 * Class GenericTextMapper - This class takes a word and calls a generic service to populate an array of synonyms in a synonym
 * object
 */
class GenericTextMapper implements TextMapper{

  private $synonyms;
  private $synonym;

  // Placeholder function for testing
  function get_synonyms( $words ) {
    if ( $words === null ) {
      return null;
    }

    foreach ( $words as &$word ) {
      $synonym = new Synonym();
      $synonym->word = $word;
      // Use service to populate synonym array
      $synonym->synonyms = array( 'synonym1', 'synonym2', 'synonym3' );
      $synonyms[] = $synonym;
    }

    return $synonyms;
  }
}
