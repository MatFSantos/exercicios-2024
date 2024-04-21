<?php

namespace Chuva\Php\WebScrapping;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

/**
 * Runner for the Webscrapping exercice.
 */
class Main {

  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void {
    $dom = new \DOMDocument('1.0', 'utf-8');
    $dom->loadHTMLFile(__DIR__ . '/../../assets/origin.html');

    $papers = (new Scrapper())->scrap($dom);

    $writer = WriterEntityFactory::createXLSXWriter();
    $writer->openToFile(__DIR__ . '/../../assets/model-result.xlsx');

    // cabeçalho feito com base na assets/model.xlsx.
    $headerRow = WriterEntityFactory::createRowFromArray([
      'ID', 'Title', 'Type',
      'Author 1', 'Author 1 Institution',
      'Author 2', 'Author 2 Institution',
      'Author 3', 'Author 3 Institution',
      'Author 4', 'Author 4 Institution',
      'Author 5', 'Author 5 Institution',
      'Author 6', 'Author 6 Institution',
      'Author 7', 'Author 7 Institution',
      'Author 8', 'Author 8 Institution',
      'Author 9', 'Author 9 Institution'
    ]);

    $writer->addRow($headerRow);

    foreach ($papers as $paper) {
      $dataRow = WriterEntityFactory::createRowFromArray([
        $paper->id,
        $paper->title,
        $paper->type
      ]);
      foreach ($paper->authors as $author) {
        $dataRow->addCell(WriterEntityFactory::createCell($author->name));
        $dataRow->addCell(WriterEntityFactory::createCell($author->institution));
      }
      $writer->addRow($dataRow);
    }
    $writer->close();
  }

}
