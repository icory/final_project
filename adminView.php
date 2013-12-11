<?php
$debug = false;
if ($debug) print "<p>DEBUG MODE IS ON</p>";

$baseURL = "http://www.uvm.edu/~icory/";
$folderPath = "cs148/assignment7.1/";
// full URL of this form
$yourURL = $baseURL . $folderPath . "adminView.php";

require_once("connect.php");

//#############################################################################
// minor security check

if (isset($_POST["btnSubmit"])) {
    $fromPage = getenv("http_referer");

    if ($debug)
        print "<p>From: " . $fromPage . " should match ";
        print "<p>Your: " . $yourURL;

    if ($fromPage != $yourURL) {
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>");
    }

}

include ("top.php");

$ext = pathinfo(basename($_SERVER['PHP_SELF']));
$file_name = basename($_SERVER['PHP_SELF'], '.' . $ext['extension']);

print '<body id="' . $file_name . '">';

include ("header.php");
?>

<section>

<div class="container">
<form action="adminUpdate.php" method="post" id="adminSelect">

<?php
//make a query to get all the poets
$sql  = 'SELECT * ';
$sql .= 'FROM tblClient ';
//$sql .= 'WHERE  ';
$sql .= 'ORDER BY pkClientID';
if ($debug) print "<p>sql ". $sql;

$stmt = $db->prepare($sql);
            
$stmt->execute(); 

$clients = $stmt->fetchAll(); 
if($debug){ print "<pre>"; print_r($clients); print "</pre>";}

// build list box
print '<fieldset class="listbox"><legend>Clients</legend><select name="lstClients">';

foreach ($clients as $client) {
    print '<option value="' . $client['pkClientID'] . '">' . $client['pkClientID'] . ' ' . $client['fldFirstName'] . ' ' . $client['fldLastName'] . ' ' . $client['fldEmail'] . "</option>\n";
}

print "</select>\n";

?>

</fieldset>

<fieldset class="buttons">
  <input type="submit" id="cmdSelect" name="cmdSelect" value="Select" class="button">
</fieldset>                    
</form>

<form action="adminUpdate.php" method="post">
  <input type="submit" name="cmdNew" value="Add New" />
</form>

<?php
  if ($debug)
    print "<p>END OF PROCESSING</p>";
      ?>
</section>
</div>


<?
include ("footer.php");
?>

</body>
</html>