<?php
/* the purpose of this page is to display a form to allow a person to either
 * add a new record if not pk was passed in or to update a record if a pk was
 * passed in.
 * 
 * notice i have more than one submit button on the form and i need to make
 * sure they have different names
 * 
 * Written By: Robert Erickson robert.erickson@uvm.edu
 * Last updated on: November 5, 2013
 * 
 * 
 */

//-----------------------------------------------------------------------------
// 
// Initialize variables
//  


$debug = false;
if (isset($_GET["debug"])) {
    $debug = true;
}

include("connect.php");

$baseURL = "http://www.uvm.edu/~icory/";
$folderPath = "cs148/assignment7.1/";
// full URL of this form
$yourURL = $baseURL . $folderPath . "adminUpdate.php";

$fromPage = getenv("http_referer");

if ($debug) {
    print "<p>From: " . $fromPage . " should match ";
    print "<p>Your: " . $yourURL;
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// initialize my form variables either to what is in table or the default 
// values.
// display record to update
if (isset($_POST["lstClients"])) {
    

    // you may want to add another security check to make sure the person
    // is allowed to delete records.
    
    $id = htmlentities($_POST["lstClients"], ENT_QUOTES);

    $sql = "SELECT * ";
    $sql .= "FROM tblClient ";
    $sql .= "WHERE pkClientId=" . $id;

    if ($debug)
        print "<p>sql " . $sql;

    $stmt = $db->prepare($sql);

    $stmt->execute();

    $clients = $stmt->fetchAll();
    if ($debug) {
        print "<pre>";
        print_r($clients);
        print "</pre>";
    }

    foreach ($clients as $client) {
        $firstName = $client["fldFirstName"];
        $lastName = $client["fldLastName"];
        $organization = $client["fldOrganization"];
        $address = $client["fldAddress"];
        $city = $client["fldCity"];
        $state = $client["fldState"];
        $country = $client["fldCountry"];
        $postalCode = $client["fldPostalCode"];
        $email = $client["fldEmail"];
        $phone = $client["fldPhone"];
    }
} else { //default values

    $id = "";
    $firstName = "";
    $lastName = "";

} // end isset lstClients

//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// simple deleting record. 
if (isset($_POST["cmdDelete"])) {
//-----------------------------------------------------------------------------
// 
// Checking to see if the form's been submitted. if not we just skip this whole 
// section and display the form
// 
//#############################################################################
// minor security check
    if ($fromPage != $yourURL) {
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>");
    }

    // you may want to add another security check to make sure the person
    // is allowed to delete records.
    
    $delId = htmlentities($_POST["deleteId"], ENT_QUOTES);

    // I may need to do a select to see if there are any related records.
    // and determine my processing steps before i try to code.

    $sql = "DELETE ";
    $sql .= "FROM tblClient ";
    $sql .= "WHERE pkClientID=" . $delId;

    if ($debug)
        print "<p>sql " . $sql;

    $stmt = $db->prepare($sql);

    $DeleteData = $stmt->execute();

    // at this point you may or may not want to redisplay the form
    if($DeleteData){
        header('Location: adminView.php');
        exit();
    }
}

//-----------------------------------------------------------------------------
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// if form has been submitted, validate the information both add and update
if (isset($_POST["btnSubmit"])) {
    if ($fromPage != $yourURL) {
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>");
    }
    
    // initialize my variables to the forms posting	
    $id = htmlentities($_POST["id"], ENT_QUOTES);
    $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES);
    $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES);
    $organization = htmlentities($_POST["txtOrganization"],ENT_QUOTES,"UTF-8");
    $address = htmlentities($_POST["txtAddress"],ENT_QUOTES,"UTF-8");
    $city = htmlentities($_POST["txtCity"],ENT_QUOTES,"UTF-8");
    $state = htmlentities($_POST["lstState"],ENT_QUOTES,"UTF-8");
    $country = htmlentities($_POST["lstCountry"],ENT_QUOTES,"UTF-8");
    $postalCode = htmlentities($_POST["txtPostalCode"],ENT_QUOTES,"UTF-8");
    $email = htmlentities($_POST["emlEmail"],ENT_QUOTES,"UTF-8");
    $phone = htmlentities($_POST["telPhone"],ENT_QUOTES,"UTF-8");
    
    // Error checking forms input
    include ("validation_functions.php");

    $errorMsg = array();

    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    // begin testing each form element 
    if ($firstName == "") {
        $errorMsg[] = "Please enter your First Name";
    } else {
        $valid = verifyAlphaNum($firstName); /* test for non-valid  data */
        if (!$valid) {
            $error_msg[] = "First Name must be letters and numbers, spaces, dashes and ' only.";
        }
    }

    if ($lastName == "") {
        $errorMsg[] = "Please enter your Last Name";
    } else {
        $valid = verifyAlphaNum($lastName); /* test for non-valid  data */
        if (!$valid) {
            $error_msg[] = "Last Name must be letters and numbers, spaces, dashes and ' only.";
        }
    }

    //- end testing ---------------------------------------------------
    
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    // there are no input errors so form is valid now we need to save 
    // the information checking to see if it is an update or insert
    // query based on the hidden html input for id
    if (!$errorMsg) {
        
        if ($debug)
            echo "<p>Form is valid</p>";

        if (isset($_POST["id"])) { // update record
            $sql = "UPDATE ";
            $sql .= "tblClient SET ";
            $sql .= "fldFirstName='$firstName', ";
            $sql .= "fldLastName='$lastName', ";
            $sql .= "fldOrganization='$organization', ";
            $sql .= "fldAddress='$address', ";
            $sql .= "fldCity='$city', ";
            $sql .= "fldState='$state', ";
            $sql .= "fldCountry='$country', ";
            $sql .= "fldPostalCode='$postalCode', ";
            $sql .= "fldEmail='$email', ";
            $sql .= "fldPhone='$phone' ";
            $sql .= "WHERE pkClientId=" . $id;
        } else { // insert record
            $sql = 'INSERT INTO tblClient (fldFirstName, fldLastName, fldOrganization, fldAddress, fldCity, fldState, fldCountry, fldPostalCode, fldEmail, fldPhone) ';
            $sql.= 'VALUES ("' . $firstName . '","' . $lastName . '","' . $organization . '","' . $address . '","' . $city . '","' . $state . '","' . $country . '","' . $postalCode . '","' . $email . '","' . $phone . '");';
        }
        // notice the SQL is basically the same. the above code could be replaced
        // insert ... on duplicate key update but since we have other procssing to
        // do i have split it up.

        if ($debug)
            echo "<p>SQL: " . $sql . "</p>";

        $stmt = $db->prepare($sql);

        $enterData = $stmt->execute();

        // Processing for other tables falls into place here. I like to use
        // the same variable $sql so i would repeat above code as needed.
        if ($debug){
            print "<p>Record has been updated";
        }
        
        // update or insert complete
        if($enterData){
            header('Location: adminView.php');
            exit();
        }
        
    }// end no errors	
} // end isset btnSubmit

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// display any errors at top of form page
if ($errorMsg) {
    echo "<ul>\n";
    foreach ($errorMsg as $err) {
        echo "<li style='color: #ff6666'>" . $err . "</li>\n";
    }
    echo "</ul>\n";
} //- end of displaying errors ------------------------------------

include ("top.php");

$ext = pathinfo(basename($_SERVER['PHP_SELF']));
$file_name = basename($_SERVER['PHP_SELF'], '.' . $ext['extension']);

print '<body id="' . $file_name . '">';

include ("header.php");

print '<div class="container">';

if ($id != "") {
    print "<h2>Edit Client Information</h2>";
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    // display a delete option
    ?>
    <form action="<? print $_SERVER['PHP_SELF']; ?>" method="post">
        <fieldset>
            <input type="submit" name="cmdDelete" value="Delete" />
            <?php print '<input name= "deleteId" type="hidden" id="deleteId" value="' . $id . '"/>'; ?>
        </fieldset>	
    </form>
    <?
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^% 
} else {
    print "<h2>Add Client Information</h2>";
}
?>

<form action="<? print $_SERVER['PHP_SELF']; ?>" method="post">
    <fieldset>
      <label for="txtFirstName">First Name</label>
      <input name="txtFirstName" type="text" size="20" id="txtFirstName" <? print "value='$firstName'"; ?>><br>

      <label for="txtLastName">Last Name</label>
      <input type="name" id="txtLastName" name="txtLastName" placeholder="" <? print "value='$lastName'"; ?>>
      <br>

      <label for="txtOrganization">Organization</label>
      <input type="text" id="txtOrganization" name="txtOrganization" placeholder="" <? print "value='$organization'"; ?>>
      <br>

      <label for="txtAddress">Address</label>
      <input type="text" id="txtAddress" name="txtAddress" placeholder="" <? print "value='$address'"; ?>>
      <br>

      <label for="txtCity">City</label>
      <input type="text" id="txtCity" name="txtCity" placeholder="" <? print "value='$city'"; ?>>
      <br>

      <label for="lstState">State</label>
      <select name="lstState" id="lstState">
      <option value="<? print "$state"; ?>" selected><? print "$state"; ?></option>
      <option value="US">-U.S. States-</option>
      <option value="AL">Alabama</option>
      <option value="AK">Alaska</option>
      <option value="AZ">Arizona</option>
      <option value="AR">Arkansas</option>
      <option value="CA">California</option>
      <option value="CO">Colorado</option>
      <option value="CT">Connecticut</option>
      <option value="DE">Delaware</option>
      <option value="DC">District of Columbia</option>
      <option value="FL">Florida</option>
      <option value="GA">Georgia</option>
      <option value="HI">Hawaii</option>
      <option value="ID">Idaho</option>
      <option value="IL">Illinois</option>
      <option value="IN">Indiana</option>
      <option value="IA">Iowa</option>
      <option value="KS">Kansas</option>
      <option value="KY">Kentucky</option>
      <option value="LA">Louisiana</option>
      <option value="ME">Maine</option>
      <option value="MD">Maryland</option>
      <option value="MA">Massachusetts</option>
      <option value="MI">Michigan</option>
      <option value="MN">Minnesota</option>
      <option value="MS">Mississippi</option>
      <option value="MO">Missouri</option>
      <option value="MT">Montana</option>
      <option value="NE">Nebraska</option>
      <option value="NV">Nevada</option>
      <option value="NH">New Hampshire</option>
      <option value="NJ">New Jersey</option>
      <option value="NM">New Mexico</option>
      <option value="NY">New York</option>
      <option value="NC">North Carolina</option>
      <option value="ND">North Dakota</option>
      <option value="OH">Ohio</option>
      <option value="OK">Oklahoma</option>
      <option value="OR">Oregon</option>
      <option value="PA">Pennsylvania</option>
      <option value="RI">Rhode Island</option>
      <option value="SC">South Carolina</option>
      <option value="SD">South Dakota</option>
      <option value="TN">Tennessee</option>
      <option value="TX">Texas</option>
      <option value="UT">Utah</option>
      <option value="VT">Vermont</option>
      <option value="VA">Virginia</option>
      <option value="WA">Washington</option>
      <option value="WV">West Virginia</option>
      <option value="WI">Wisconsin</option>
      <option value="WY">Wyoming</option>
      <option value="MEX">-Estados de Mexico-</option>
      <option value="Aguascalientes">Aguascalientes</option>
      <option value="Baja California">Baja California</option>
      <option value="Baja California Sur">Baja California Sur</option>
      <option value="Campeche">Campeche</option>
      <option value="Chiapas">Chiapas</option>
      <option value="Chihuahua">Chihuahua</option>
      <option value="Coahuila">Coahuila</option>
      <option value="Colima">Colima</option>
      <option value="Distrito Federal">Distrito Federal</option>
      <option value="Durango">Durango</option>
      <option value="Guanajuato">Guanajuato</option>
      <option value="Guerrero">Guerrero</option>
      <option value="Hidalgo">Hidalgo</option>
      <option value="Jalisco">Jalisco</option>
      <option value="Mexico">Mexico</option>
      <option value="Michoacan">Michoac&aacute;n</option>
      <option value="Morelos">Morelos</option>
      <option value="Nayarit">Nayarit</option>
      <option value="Nuevo Leon">Nuevo Le&oacute;n</option>
      <option value="Oaxaca">Oaxaca</option>
      <option value="Puebla">Puebla</option>
      <option value="Queretaro">Quer&eacute;taro</option>
      <option value="Quintana Roo">Quintana Roo</option>
      <option value="San Luis Potosi">San Luis Potosi</option>
      <option value="Sinaloa">Sinaloa</option>
      <option value="Sonora">Sonora</option>
      <option value="Tabasco">Tabasco</option>
      <option value="Tamaulipas">Tamaulipas</option>
      <option value="Tlaxcala">Tlaxcala</option>
      <option value="Veracruz">Veracruz</option>
      <option value="Yucatan">Yucatan</option>
      <option value="Zacatecas">Zacatecas</option>
      </select>
      <br>

      <label for="lstCountry">Country</label>
      <select name="lstCountry" id="lstCountry">
      <option value="<? print "$country"; ?>" selected><? print "$country"; ?></option>

      <option value="USA">United States</option>
      <option value="MEX">Mexico</option>
      </select>
      <br>

      <label for="txtPostalCode">Postal Code</label>
      <input type="text" id="txtPostalCode" name="txtPostalCode" placeholder="" <? print "value='$postalCode'"; ?>>
      <br>

      <label for="emlEmail">Email</label>
      <input type="email" id="emlEmail" name="emlEmail" value="<?php echo $email; ?>" placeholder="" <? print "value='$email'"; ?>>
      <br>

      <label for="telPhone">Phone</label>
      <input type="tel" id="telPhone" name="telPhone" placeholder="" <? print "value='$phone'"; ?>>
      <br>

      </fieldset>
<!-- Hide
      <fieldset>

      <label for="txtDocumentTitle">Document Title</label>
      <input type="text" name="txtDocumentTitle" id="txtDocumentTitle" <? print "value='$docTitle'"; ?>><br>

      <label for="txtDocumentType">Document Type</label>
      <input type="text" id="txtDocumentType" name="txtDocumentType" placeholder="" <? print "value='$docType'"; ?>>
      <br>

      <label for="txtWordCount">Word Count</label>
      <input type="number" id="txtWordCount" name="txtWordCount" placeholder="" <? print "value='$docWordCount'"; ?>>
      <br>

      <label for="datepicker">Date Required</label>
      <input type="date" id="datepicker" name="datepicker" placeholder="" <? print "value='$doc'"; ?>>
      <br>

      </fieldset>
End Hide -->
        <?
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// if there is a record then we need to be able to pass the pk back to the page
        if ($id != "")
            print '<input name= "id" type="hidden" id="id" value="' . $id . '"/>';
        ?>
        <input type="submit" name="btnSubmit" value="Submit" />
    </fieldset>		
</form>
</div> <!-- end div.container -->
<?php

include ("footer.php");
?>
</body>
</html>