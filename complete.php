<!DOCTYPE html>
<html>
<head>
  <title>EmailCat | ConCATenate your email data now!</title>
  <link href='https://fonts.googleapis.com/css?family=Oswald:300,400' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body>

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

    $extractedDir = retrieveExtractedDir();

    $allFiles = retrieveFileList($extractedDir);

    $allData = retrieveData($allFiles, $extractedDir);

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

</body>
</html>
