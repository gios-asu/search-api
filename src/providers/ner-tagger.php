<?php

namespace SearchApi\Providers;
use SearchApi\Models\Keyword;
use SearchApi\Services\Tagger;
use StanfordNLP\NERTagger as StanfordNERTagger;
use Nectary\Configuration as Configuration;

/**
 * NerTagger - This is a tagger implementation that uses the Named Entity Recognizer and returns an array of keywords.

 * @seeAlso: http://nlp.stanford.edu/software/CRF-NER.shtml
 */
class NerTagger implements Tagger {
  // Convert a search string into an array of Keyword objects
  public function get_tags( $request_string = '' ) {
    // Call the NER Tagger service
    $tagger_results = $this->tagger_service( $request_string );
    // Convert results into keywords
    $keywords = $this->results_to_keywords( $tagger_results );

    return $keywords;
  }

  // Call the NER service and return the results
  public function tagger_service( $request_string = '' ) {
    // Return null if there is no request
    if ( empty( $request_string ) ) {
      return null;
    }

    // Get path to Stanford NER from config.
    // TODO: Configuration path in constructor (?)
    Configuration::set_configuration_path( 'config.conf' );
    $stanfordNerPath = realpath( rtrim(
        Configuration::get_instance()->get( 'StanfordNerPath', 'lib/stanford-ner/' )
    ) );

    $tagger = new \StanfordNLP\NERTagger(
        $stanfordNerPath . '/classifiers/english.all.3class.distsim.crf.ser.gz',
        $stanfordNerPath . '/stanford-ner.jar'
    );

    // Explode the request and push it through the tagger
    $tagger_results = $tagger->tag( explode( ' ', $request_string ) );

    return $tagger_results;
  }

  // Interpret the tagger results into Keyword objects
  public function results_to_keywords( $tagger_results ) {
    // Return null if there are no results
    if ( empty( $tagger_results ) ) {
      return null;
    }

    // This works properly, but does not handle 'relevance' yet (temporary)
    foreach ( $tagger_results as $result ) {
      $keywords[] = new Keyword( $result[0], $result[1], 1.0 );
    }

    return $keywords;
  }
}
