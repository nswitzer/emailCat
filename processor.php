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

extractArchive($fileName);

// Create array of messages to send to retrieveData
// @TODO: Autodetect location of files in unarchived directory
$allFiles = array_diff(scandir('data/smallset'), array('.', '..'));

$allData = retrieveData($allFiles);

outputCsv('emaildata.csv', $allData);

/**
 * Parses a directory of emails and retrieves data, sender and subject data
 * from headers.
 * @param $emailDirectory
 * @return array of all data, sender and subject data
 */
function retrieveData($emailMessages) {
  $outputData = array();

  // Loop through array of messages and create arrays of data for output
  // @TODO: Build emailPath more dynamically
  foreach ($emailMessages as $message) {
    // Define the path to the file and create a parser
    $emailPath = 'data/smallset/' . $message;
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
  return $outputData;
}

/**
 * Extracts uploaded archive file to data directory
 * @param $archiveName
 */
function extractArchive($archiveName) {
  try {
    $emailArchive = new PharData('data/' . $archiveName);
    $emailArchive->extractTo('data');
  } catch (Exception $e) {
    // handle errors
  }
}

/**
 * Builds and outputs a CSV file containing data, sender and subject data for
 * all emails included in the uploaded archive
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

?>