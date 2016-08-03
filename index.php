<?php

// Extract archive
try {
  $emailArchive = new PharData('data/sampleEmailstar.gz');
  $emailArchive->extractTo('data');
} catch (Exception $e) {
  // handle errors
}

// Create array of messages

// Loop through array of messages and create arrays of data for output

// Write output data to CSV file

?>