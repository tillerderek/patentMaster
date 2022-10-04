<?php

ini_set('display_errors', 1);

$patNumber = $_POST['patNumber'];


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://developer.uspto.gov/ibd-api/v1/application/grants?patentNumber=' . $patNumber);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$json = curl_exec($ch);

curl_close($ch);

$arr = json_decode($json, true);

?>

<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PatentMaster</title>
  <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<?php

$noResults = $arr["recordTotalQuantity"];

// if noResults do this
if ($noResults == 0) {

?>

  <p>ya blew it!</p>

<?php
  // else do this
} else {

  $smallWordsArray = array(
    'of', 'a', 'the', 'and', 'an', 'or', 'nor', 'but', 'is', 'if', 'then', 'else', 'when',
    'at', 'from', 'by', 'on', 'off', 'for', 'in', 'out', 'over', 'to', 'into', 'with'
  );

  $results = $arr["results"][0];

  $abstract = $results["abstractText"][0];

  $title = $results["inventionTitle"];
  $words = explode(' ', $title);
  foreach ($words as $key => $word) {
    if (!$key or !in_array($word, $smallWordsArray))
      $words[$key] = ucwords($word);
  }
  $newTitle = implode(' ', $words);


  $date = date_create_from_format("m-d-Y", $results["grantDate"]);


  $patentNumber = $results["patentNumber"];
  $patentNumberFormatted = (int)$patentNumber;
  $inventors = $results["inventorNameArrayText"];

?>
  <div id="tableContainer">
    <table>
      <tr>
        <td>Inventors:</td>
        <td><?php foreach ($inventors as $name) {
              list($lname, $fname) = preg_split('/ /', $name, 2);
              echo $fname . ' ' . $lname . "<br>";
            } ?></td>
      </tr>
      <tr>
        <td>Patent Number:</td>
        <td><?php echo number_format($patentNumberFormatted) ?></td>
      </tr>
      <tr>
        <td>Date:</td>
        <td><?php echo date_format($date, "F j, Y") ?></td>
      </tr>
      <tr>
        <td>Title:</td>
        <td><?php echo $newTitle ?></td>
      </tr>
      <tr>
        <td>Abstract:</td>
        <td><?php echo $abstract ?></td>
      </tr>
    </table>
  </div>

</html>
<?php } ?>