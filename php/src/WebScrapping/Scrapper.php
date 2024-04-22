<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;

/**
 * Does the scrapping of a webpage.
 */
class Scrapper {

  /**
   * Loads paper information from the HTML and returns the array with the data.
   */
  public function scrap(\DOMDocument $dom): array {
    $ancors = $dom->getElementsByTagName('a');
    $papers = $this->getPapers($ancors, 'paper-card');

    return $papers;
  }

  /**
   * Catches all documents on assigned nodes.
   */
  private function getPapers($nodes, $class): array {
    $papers = [];
    foreach ($nodes as $node) {
      if ($this->isValidNode($node, $class)) {
        $title = $this->getPaperTitle($node);
        $authors = $this->getAuthors($node);
        $type = $this->getType($node);
        $id = $this->getId($node);
        $papers[] = new Paper($id, $title, $type, $authors);
      }
    }
    return $papers;
  }

  /**
   * Verifies if a node has a class.
   */
  private function isValidNode($node, $class): bool {
    $node_class = $node->getAttribute('class');
    return $node_class && strpos($node_class, $class) !== FALSE;
  }

  /**
   * Catches a Paper title.
   */
  private function getPaperTitle($node): string {
    $h4s = $node->getElementsByTagName('h4');
    if ($h4s->length) {
      return $h4s[0]->nodeValue;
    }
    return "-";
  }

  /**
   * Catches the Paper authors with yours instituitions.
   */
  private function getAuthors($node): array {
    $div = $this->getDiv($node, 'authors');
    $authors = [];
    if ($div === NULL) {
      return $authors;
    }
    foreach ($div->getElementsByTagName('span') as $span) {
      $authors[] = new Person($span->nodeValue, $span->getAttribute('title'));
    }
    return $authors;
  }

  /**
   * Catches a Div in a node list according to class.
   */
  private function getDiv($node, $class) {
    $divs = $node->getElementsByTagName('div');
    foreach ($divs as $div) {
      if ($this->isValidNode($div, $class)) {
        return $div;
      }
    }
    return NULL;
  }

  /**
   * Catches the Paper type.
   */
  private function getType($node): string {
    $div = $this->getDiv($node, 'tags mr-sm');
    if ($div === NULL) {
      return "---";
    }
    return $div->nodeValue;
  }

  /**
   * Catches the Paper ID.
   */
  private function getId($node): string {
    $div = $this->getDiv($node, 'volume-info');
    if ($div === NULL) {
      return "---";
    }
    $id = $div->nodeValue;
    return intval($id);
  }

}
