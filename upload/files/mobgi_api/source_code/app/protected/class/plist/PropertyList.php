<?php

/**
 * plist.php
 * Property List parser class for PHP
 * Shamelessly ripped from Theo Hultberg
 * http://blog.iconara.net/2007/05/08/php-plist-parsing/
 * Class wrapper by Filipp Lepalaan <filipp@mac.com>
 * Usage: $plist = new PropertyList("/path/to/file.plist")
 *        $array = $plist->toArray()
 */
class PropertyList
{
  function __construct($xmlFile) {
    if (!file_exists($xmlFile)) {
      echo "{$xmlFile}: no such file";
      return false;
    }
    $document = new DOMDocument();
    $document->load($xmlFile);
    $plistNode = $document->documentElement;
    $this->root = $plistNode->firstChild;
    while ($this->root->nodeName == "#text") {
      $this->root = $this->root->nextSibling;
    }
    return $this;
  }
  
  /**
   * Return a PropertyList as array
   */
  function toArray() {
    return $this->parseValue($this->root);
  }
  
  /**
   * Route plist key value to the correct parsing method
   */
  function parseValue($valueNode) {
    $valueType = $valueNode->nodeName;
    $transformerName = "parse_$valueType";
    if (is_callable(array($this, $transformerName))) {
      return call_user_func(array($this, $transformerName), $valueNode);
    }
    return null;
  }

  function parse_integer($integerNode) {
    return $integerNode->textContent;
  }

  function parse_string($stringNode) {
    return $stringNode->textContent;
  }

  function parse_date($dateNode) {
    return $dateNode->textContent;
  }

  function parse_true($trueNode) {
    return true;
  }

  function parse_false($trueNode) {
    return false;
  }

  function parse_dict($dictNode) {
    $dict = array();
    for ($node = $dictNode->firstChild; $node != null; $node = $node->nextSibling) {
      if ($node->nodeName == 'key') {
        $key = $node->textContent;
        $valueNode = $node->nextSibling;
        while ($valueNode->nodeType == XML_TEXT_NODE) {
          $valueNode = $valueNode->nextSibling;
        }
        $value = $this->parseValue($valueNode);
        $dict[$key] = $value;
      }
    }
    return $dict;
  }

  function parse_array($arrayNode) {
    $array = array();
    for ($node = $arrayNode->firstChild; $node != null; $node = $node->nextSibling) {
      if ($node->nodeType == XML_ELEMENT_NODE) {
        array_push($array, $this->parseValue($node));
      }
    }
    return $array;
  }
}

?>