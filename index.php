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
$allFiles = array_diff(scandir('data/smallset'), array('.', '..'));

// ...and empty arrays to fill with extracted content
$allDatesSent = array();
$allSenders = array();
$allSubjects = array();

// Loop through array of messages and create arrays of data for output
foreach ($allFiles as $file) {
  $emailPath = 'data/smallset/' . $file;
  $emailParser = new PlancakeEmailParser(file_get_contents($emailPath));

  $emailDateSent = $emailParser->getHeader('Date');
  $emailSender = $emailParser->getHeader('From');
  $emailSubject = $emailParser->getSubject();

  $allDatesSent[] = $emailDateSent;
  $allSenders[] = $emailSender;
  $allSubjects[] = $emailSubject;
}

// Write output data to CSV file

?>