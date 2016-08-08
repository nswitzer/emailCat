<title>EmailCat | ConCATenate your email data now!</title>
<link rel="stylesheet" href="css/main.css" type="text/css">

<?php

include('api.php');

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
    storeArchive($fileTmp, $fileName);

    extractArchive($fileName);

    // Create array of messages to send to retrieveData
    // @TODO: Autodetect location of files in unarchived directory
    $allFiles = array_diff(scandir('data/smallset'), array('.', '..'));

    $allData = retrieveData($allFiles);

    outputCsv('emaildata.csv', $allData);
  } else {
    echo '<div class="messages failure"><ul>';
    foreach ($errors as $error) {
      echo '<li>' . $error . '</li>';
    }
    echo '</ul></div>';
    exit();
  }
}

?>
