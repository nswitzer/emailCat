<?php

require __DIR__ . '/vendor/autoload.php';

/**
 * Checks if data directory exists and moves uploaded file to a permanent
 * storage location.
 * @param $fileLocation
 * @param $fileName
 */
function storeArchive($fileLocation, $fileName) {
  if (!file_exists('data')) {
    mkdir('data', 0777);
  }
  move_uploaded_file($fileLocation, 'data/' . $fileName);
}

/**
 * Extracts uploaded archive file to data directory
 * @param $archiveName
 */
function extractArchive($archiveName) {
  try {
    $emailArchive = new PharData('data/' . $archiveName);
    $emailArchive->extractTo('data/extracted');
  } catch (Exception $e) {
    // handle errors
  }
}

/**
 * Scan extracted directory and return path to it's contents
 * @return string
 */
function retrieveExtractedDir() {
  $extractedDir = array_diff(scandir('data/extracted'), array('.', '..'));
  $extractedDir = 'data/extracted/' . $extractedDir[2];
  return $extractedDir;
}

/**
 * Given the path to the archive's extracted dir, return an array of all
 * files contained within the extracted dir.
 * @param $path
 * @return array
 */
function retrieveFileList ($path) {
  $fileList = array_diff(scandir($path), array('.', '..'));
  return $fileList;
}

/**
 * Parses a directory of emails and retrieves data, sender and subject data
 * from headers.
 * @param $emailDirectory
 * @return array of all data, sender and subject data
 */
function retrieveData($emailMessages, $extractedDir) {
  $outputData = array();

  // Loop through array of messages and create arrays of data for output
  // @TODO: Receive extracted directory path as function parameter
  foreach ($emailMessages as $message) {
    // Define the path to the file and create a parser
    $emailPath = $extractedDir . '/' . $message;
    $emailParser = new PlancakeEmailParser(file_get_contents($emailPath));

    // Temp var to build associative arrays of output data
    $item = array();

    // Grab the content we need from the email header and add to our temp array
    $item['filePath'] = $extractedDir . '/' . $message;
    $item['sendDate'] = $emailParser->getHeader('Date');
    $item['sender'] = $emailParser->getHeader('From');
    $item['subject'] = $emailParser->getHeader('Subject');

    // Add data to our final array of output data
    $outputData[] = $item;
  }
  return $outputData;
}

/**
 * Builds and outputs a CSV file containing date, sender and subject data for
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
    echo '<div class="messages success">Success!</div>';
  }
}


/**
 * Builds and outputs a txt file containing message path, date, sender and
 * subject data for all emails included in the uploaded archive.
 * @param $fileName
 * @param $emailData
 */
function outputTxt($fileName, $emailData) {
  $file = fopen('data/' . $fileName, 'w');
  foreach ($emailData as $values) {
    fwrite($file, implode('|', $values) . PHP_EOL);
  }
  fclose($file);
  echo '<div class="messages success">Success!</div>';
}

?>