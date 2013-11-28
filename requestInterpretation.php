<?php include('top.php'); ?>

<body id="">

<?php include ('header.php'); ?>

<section>
  <a href="requestServices.php">Request Services</a>
  >
 Request Interpretation Services
</section>

<section>
  <form id="contact">
    <legend>Request an Interpreter</legend>
    <fieldset>
      <legend>Step 1: Identification</legend>

      <label for="txtTitle">Title</label>
      <select id="txtTitle" name="txtTitle">
      <option value="none" selected>--</option>
      <option value="Mr.">Mr.</option>
      <option value="Mrs.">Mrs.</option>
      <option value="Ms.">Ms.</option>
      </select>

      <label for="txtName">Name</label>
      <input type="name" id="txtName" name="txtName" placeholder="Preferred Name">
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

      <label for="txtState">State (if U.S.)</label>
    <select name="txtState" id="txtState">
    <option value="" selected>-Choose State-</option>
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
    </select>
      <br>

      <label for="txtProvince">Province (if applicable)</label>
      <input type="text" id="txtProvince" name="txtProvince" placeholder="">
      <br>

      <label for="txtPostalCode">Postal Code</label>
      <input type="text" id="txtPostalCode" name="txtPostalCode" placeholder="">
      <br>

      <label for="emlEmail">Email</label>
      <input type="email" id="emlEmail" name="emlEmail" placeholder="">
      <br>

      <label for="txtComment">Comment</label>
      <textarea id="txtComment" name="txtComment" wrap="physical" placeholder="Write your comment or inquiry here."></textarea>

    </fieldset>

    <fieldset>
      <legend></legend>

    </fieldset>

    <fieldset>
       <legend>Step 3: Submit Request</legend>
	<input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" tabindex="">
    </fieldset>

  </form>
</section>

<?php include ('footer.php'); ?>

</body>
</html>