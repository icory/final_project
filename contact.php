<?php
/* the purpose of this page is to display a form to allow a person to register
 * the form will be sticky meaning if there is a mistake the data previously 
 * entered will be displayed again. Once a form is submitted (to this same page)
 * we first sanitize our data by replacing html codes with the html character.
 * then we check to see if the data is valid. if data is valid enter the data 
 * into the table and we send and dispplay a confirmation email message. 
 * 
 * if the data is incorrect we flag the errors.
 * 
 * Written By: Robert Erickson robert.erickson@uvm.edu
 * Last updated on: October 10, 2013
 * 
 * 
  -- --------------------------------------------------------
  --
  -- Table structure for table `tblRegister`
  --

  CREATE TABLE IF NOT EXISTS `tblContributor` (
  `pkRegisterId` int(11) NOT NULL AUTO_INCREMENT,
  `fldEmail` varchar(65) DEFAULT NULL,
  `fldDateJoined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fldConfirmed` tinyint(1) NOT NULL DEFAULT '0',
  `fldApproved` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pkPersonId`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

 * I am using a surrogate key for demonstration, 
 * email would make a good primary key as well which would prevent someone
 * from entering an email address in more than one record.
 */

//-----------------------------------------------------------------------------
// 
// Initialize variables
//  

$debug = false;
if ($debug) print "<p>DEBUG MODE IS ON</p>";

$baseURL = "http://www.uvm.edu/~icory/";
$folderPath = "cs148/assignment7.1/";
// full URL of this form
$yourURL = $baseURL . $folderPath . "contact.php";

require_once("connect.php");

//#############################################################################
// set all form variables to their default value on the form.

$firstName="";
$lastName="";
$organization="";
$city="";
$state="-Choose State-";
$country="";
$postalCode="";
$email="icory@uvm.edu";
$phone="";
$subject="-Select One-";
$comment="";

//#############################################################################
// 
// flags for errors

$firstNameERROR = false;
$lastNameERROR = false;
$emailERROR = false;
$miscERROR = false;


//#############################################################################
//  
$mailed = false;
$messageA = "";
$messageB = "";
$messageC = "";


//-----------------------------------------------------------------------------
// 
// Checking to see if the form's been submitted. if not we just skip this whole 
// section and display the form
// 
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


//#############################################################################
// replace any html or javascript code with html entities
//

    $firstName = htmlentities($_POST["txtFirstName"],ENT_QUOTES,"UTF-8");
    $lastName = htmlentities($_POST["txtLastName"],ENT_QUOTES,"UTF-8");
    $organization = htmlentities($_POST["txtOrganization"],ENT_QUOTES,"UTF-8");
    $address = htmlentities($_POST["txtAddress"],ENT_QUOTES,"UTF-8");
    $city = htmlentities($_POST["txtCity"],ENT_QUOTES,"UTF-8");
    $state = htmlentities($_POST["lstState"],ENT_QUOTES,"UTF-8");
    $country = htmlentities($_POST["lstCountry"],ENT_QUOTES,"UTF-8");
    $postalCode = htmlentities($_POST["txtPostalCode"],ENT_QUOTES,"UTF-8");
    $email = htmlentities($_POST["emlEmail"],ENT_QUOTES,"UTF-8");
    $phone = htmlentities($_POST["telPhone"],ENT_QUOTES,"UTF-8");
    $subject = htmlentities($_POST["lstSubject"],ENT_QUOTES,"UTF-8");
    $comment = htmlentities($_POST["txtComment"],ENT_QUOTES,"UTF-8");


//#############################################################################
// 
// Check for mistakes using validation functions
//
// create array to hold mistakes
// 

    include ("validation_functions.php");

    $errorMsg = array();


//############################################################################
// 
// Check each of the fields for errors then adding any mistakes to the array.
//
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^       Check email address
    if (empty($email)) {
        $errorMsg[] = "Please enter your Email Address";
        $emailERROR = true;
    } else {
        $valid = verifyEmail($email); /* test for non-valid  data */
        if (!$valid) {
            $errorMsg[] = "I'm sorry, the email address you entered is not valid.";
            $emailERROR = true;
        }
    }


//############################################################################
// 
// Processing the Data of the form
//

    if (!$errorMsg) {
        if ($debug) print "<p>Form is valid</p>";

//############################################################################
//
// the form is valid so now save the information
//    
        $primaryKey = "";
        $dataEntered = false;

$date = new dateTime();
$timestamp=$date->format('Y-m-d H:i:s');
        
        try {
            $db->beginTransaction();
           
            $sql = 'INSERT INTO tblClient (fldFirstName, fldLastName, fldOrganization, fldAddress, fldCity, fldState, fldCountry, fldPostalCode, fldEmail, fldPhone) ';
            $sql.= 'VALUES ("' . $firstName . '","' . $lastName . '","' . $organization . '","' . $address . '","' . $city . '","' . $state . '","' . $country . '","' . $postalCode . '","' . $email . '","' . $phone . '");';
            $stmt = $db->prepare($sql);
            if ($debug) print "<p>sql ". $sql;
            $stmt->execute();

            $primaryKey = $db->lastInsertId();
            if ($debug) print "<p>pk= " . $primaryKey;

            $sql = 'INSERT INTO tblRequest (fkClientID, fldTimestamp, fldComment, fldSubject) ';
            $sql.= 'VALUES ("' . $primaryKey . '","' . $timestamp . '","' . $comment . '","' . $subject . '");';

            $stmt = $db->prepare($sql);

            if ($debug) print "<p>sql ". $sql;
       
            $stmt->execute();
            

            // all sql statements are done so lets commit to our changes
            $dataEntered = $db->commit();
            if ($debug) print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $db->rollback();
            if ($debug) print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }


        // If the transaction was successful, give success message
        if ($dataEntered) {
            //#################################################################
            //
            //Put forms information into a variable to print on the screen
            //

            $messageA = "<h2>Hello " . $firstName . ". Thank you for contacting us.</h2>";

            $messageB = "<p>We have received your comment and will respond to you as soon as we are able. ";
            $messageB .= "If you are looking for the answer to a question and haven't yet visited the FAQ page, you may do so <a href='http://www.uvm.edu/~icory/cs148/assignment7.1/faq.php'>here</a>.</p>";
            $messageB .= "<p><a href='http://www.uvm.edu/~icory/cs148/assignment7.1/home.php'>X-Cultural Communications Website</a></p>";

            $messageC .= "<p><b>Email Address:</b><i>   " . $email . "</i></p>";

            //##############################################################
            //
            // email the form's information
            //
            
            $subject = "X-Cultural Communications*";
            include_once('mailMessage.php');
            $mailed = sendMail($email, $subject, $messageA . $messageB);
        } //data entered   
    } // no errors 
}// ends if form was submitted. 

    include ("top.php");

    $ext = pathinfo(basename($_SERVER['PHP_SELF']));
    $file_name = basename($_SERVER['PHP_SELF'], '.' . $ext['extension']);

    print '<body id="' . $file_name . '">';

    include ("header.php");
    ?>

    <section>

        <?
//############################################################################
//
//  In this block  display the information that was submitted and do not 
//  display the form.
//
        if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) {
            print "<h2>Your Request has ";

            if (!$mailed) {
                echo "not ";
            }

            echo "been processed</h2>";

            print "<p>A copy of this message has ";
            if (!$mailed) {
                echo "not ";
            }
            print "been sent to: " . $email . "</p>";

            echo $messageA . $messageB;
        } else {


//#############################################################################
//
// Here we display any errors that were on the form
//

            print '<div id="errors">';

            if ($errorMsg) {
                echo "<ol>\n";
                foreach ($errorMsg as $err) {
                    echo "<li>" . $err . "</li>\n";
                }
                echo "</ol>\n";
            }

            print '</div>';
            ?>
            <!--   Take out enctype line    -->
            <form action="<? print $_SERVER['PHP_SELF']; ?>"
                  enctype="multipart/form-data"
                  method="post">
    <legend>Contact Us</legend>
    <fieldset>
      <label for="txtFirstName">First Name</label>
      <input type="name" id="txtFirstName" name="txtFirstName" placeholder="">
      <br>

      <label for="txtLastName">Last Name</label>
      <input type="name" id="txtLastName" name="txtLastName" placeholder="">
      <br>

      <label for="txtOrganization">Organization</label>
      <input type="text" id="txtOrganization" name="txtOrganization" placeholder="">
      <br>

      <label for="txtAddress">Address</label>
      <input type="text" id="txtAddress" name="txtAddress" placeholder="">
      <br>

      <label for="txtCity">City</label>
      <input type="text" id="txtCity" name="txtCity" placeholder="">
      <br>

      <label for="lstState">State</label>
      <select name="lstState" id="lstState">
      <option value="" selected>-Choose State-</option>
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
      <option value="" selected>-Select Country-</option>
      <option value="USA">United States</option>
      <option value="MEX">Mexico</option>
      </select>
      <br>

      <label for="txtPostalCode">Postal Code</label>
      <input type="text" id="txtPostalCode" name="txtPostalCode" placeholder="">
      <br>

      <label for="emlEmail">Email</label>
      <input type="email" id="emlEmail" name="emlEmail" value="<?php echo $email; ?>" placeholder="">
      <br>

      <label for="telPhone">Phone</label>
      <input type="tel" id="telPhone" name="telPhone" placeholder="">
      <br>

      <label for="lstSubject">Subject</label>
      <select id="lstSubject" name="lstSubject" placeholder="Select">
      <option value="" selected>-Select One-</option>
      <option value="Trainings">Trainings</option>
      <option value="Translation">Translation Services</option>
      <option value="Interpretation">Interpretation Services</option>
      <option value="Other">Other</option>
      </select>
      <br>

      <label for="txtComment">Comment</label>
      <textarea id="txtComment" name="txtComment" wrap="physical" placeholder="Write your comment or inquiry here."></textarea>

      </fieldset>

      <fieldset class="buttons">
        <input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" class="button">
        <input type="reset" id="btnReset" name="btnReset" value="Reset Form" class="button" onclick="reSetForm()" >
      </fieldset>                    

            </form>
            <?php
        } // end body submit
        if ($debug)
            print "<p>END OF PROCESSING</p>";
        ?>
    </section>


    <?
    include ("footer.php");
    ?>

</body>
</html>