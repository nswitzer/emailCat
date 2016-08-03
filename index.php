<?php

require __DIR__ . '/vendor/autoload.php';

// Extract archive
try {
  $emailArchive = new PharData('data/sampleEmailstar.gz');
  $emailArchive->extractTo('data');
} catch (Exception $e) {
  // handle errors
}

// Create array of messages...
// @TODO: Autodetect location of files in unarchived directory
$allFiles = array_diff(scandir('data/smallset'), array('.', '..'));

// ...and an empty array to fill with extracted content

$outputData = array();
// Loop through array of messages and create arrays of data for output
// @TODO: Build emailPath more dynamically
foreach ($allFiles as $file) {
  // Define the path to the file and create a parser
  $emailPath = 'data/smallset/' . $file;
  $emailParser = new PlancakeEmailParser(file_get_contents($emailPath));

  // Temp var to build associative arrays of output data
  $item = array();

  // Grab the content we need from the email header and add to our temp array
  $item['date'] = $emailParser->getHeader('Date');
  $item['sender'] = $emailParser->getHeader('From');
  $item['subject'] = $emailParser->getSubject();

  // Add data to our final array of output data
  $outputData[] = $item;
}
k($outputData);

// Write output data to CSV file

?>