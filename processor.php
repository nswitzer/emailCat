<?php

require __DIR__ . '/vendor/autoload.php';

// Form handling
if (isset($_FILES['emailArchiveUpload'])) {
  $errors = array();
  $fileName = $_FILES['emailArchiveUpload']['name'];
  $fileTmp =$_FILES['emailArchiveUpload']['tmp_name'];
  $fileExt = strtolower(end(explode('.',$fileName)));
  $allowedExt = array('gz');

  if (in_array($fileExt, $allowedExt) == FALSE) {
    $errors[] = 'Invalid file extension!';
  }

  if (empty($errors) == TRUE) {
    move_uploaded_file($fileTmp, 'data/' . $fileName);
  }
}


// Extract archive
extractArchive($fileName);

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
  $item['sendDate'] = $emailParser->getHeader('Date');
  $item['sender'] = $emailParser->getHeader('From');
  $item['subject'] = $emailParser->getSubject();

  // Add data to our final array of output data
  $outputData[] = $item;
}
k($outputData);

function extractArchive($archiveName) {
  try {
    $emailArchive = new PharData('data/' . $archiveName);
    $emailArchive->extractTo('data');
  } catch (Exception $e) {
    // handle errors
  }
}

// Write output data to CSV file

/**
 * Pass in filename and associative data array, get a csv back.
 * @param $fileName
 * @param $emailData
 */
function outputCsv($fileName, $emailData) {
  if (isset($emailData['0'])) {
    $fp = fopen('data/' . $fileName, 'w');

    fputcsv($fp, array_keys($emailData['0']));
    foreach ($emailData as $values) {
      fputcsv($fp, $values);
    }

    fclose($fp);
  }
}

outputCsv('emaildata.csv', $outputData);

?>