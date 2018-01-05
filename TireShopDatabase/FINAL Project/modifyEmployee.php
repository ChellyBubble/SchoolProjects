<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Edit Personal Information';
  require_once('header.php');

  require_once('connectvars.php');

  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['Ssn'])) {
    echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
    exit();
  }

  // Show the navigation menu
  require_once('navMenu.php');

  // Make sure the user is  an admin user before going any further.
  if ($_SESSION['Admin_user'] == '0') {
    echo '<p class="login">User does not have access this page.</p>';
    exit();
  }
  
  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  if (isset($_GET['Ssn']) && isset($_GET['Lname']) && isset($_GET['Fname']) ) {
    // Grab the score data from the GET
    $Ssn = $_GET['Ssn'];
	$Lname = $_GET['Lname'];
    $Fname = $_GET['Fname'];
	
  }
  else if (isset($_POST['Ssn']) && isset($_POST['Lname']) && isset($_POST['Fname']) ) {
    // Grab the score data from the POST
    $Ssn = $_POST['Ssn'];
	$Lname = $_POST['Lname'];
    $Fname = $_POST['Fname'];
  }
  
  if (isset($_POST['submit'])) {
    // Grab the profile data from the POST
	$Ssn = mysqli_real_escape_string($dbc, trim($_POST['Ssn']));
	$Fname = mysqli_real_escape_string($dbc, trim($_POST['Fname']));
	$Minit = mysqli_real_escape_string($dbc, trim($_POST['Minit']));
    $Lname = mysqli_real_escape_string($dbc, trim($_POST['Lname']));
    $Salary = mysqli_real_escape_string($dbc, trim($_POST['Salary']));
    $Super_ssn = mysqli_real_escape_string($dbc, trim($_POST['Super_ssn']));
    $Dno = mysqli_real_escape_string($dbc, trim($_POST['Dno']));
    $error = false;

    // Update the profile data in the database
    if (!$error) {
      if (!empty($Fname) && !empty($Minit) && !empty($Lname) && !empty($Salary) && !empty($Dno) ) {
        // Only set the picture column if there is a new picture
        if(!empty($Super_ssn)){
			$query = "UPDATE A_EMPLOYEE SET Fname = '$Fname', Minit = '$Minit', Lname = '$Lname', Salary = '$Salary', Super_ssn = '$Super_ssn' WHERE Ssn = '" . $Ssn . "'";
      
			mysqli_query($dbc, $query);

			// Confirm success with the user
			echo '<p>Employee information has been successfully updated. </p>';
		}
		else{
			$query_2 = "UPDATE A_EMPLOYEE SET Fname = '$Fname', Minit = '$Minit', Lname = '$Lname', Salary = '$Salary' WHERE Ssn = '" . $Ssn . "'";
      
			mysqli_query($dbc, $query_2);

			// Confirm success with the user
			echo '<p>Employee information has been successfully updated. </p>';
		
		}
        mysqli_close($dbc);
        exit();
      }
      else {
        echo '<p class="error">You must enter all of the information.</p>';
      }
    }
  } // End of check for form submission
  else {
    // Grab the profile data from the database
    $query = "SELECT * FROM A_EMPLOYEE WHERE Ssn = '" . $Ssn . "'";
    $data = mysqli_query($dbc, $query);
    $row = mysqli_fetch_array($data);

    if ($row != NULL) {
	  $Fname = $row['Fname'];
	  $Minit = $row['Minit'];
	  $Lname = $row['Lname'];
      $Salary = $row['Salary'];
	  $Super_ssn = $row['Super_ssn'];
	  $Dno = $row['Dno'];
	  
    }
    else {
      echo '<p class="error">There was a problem accessing your information.</p>';
    }

  }

  mysqli_close($dbc);
?>

  <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MM_MAXFILESIZE; ?>" />
    <fieldset>
      <legend>Modify Employee Information</legend>
	  <table>
	  <tr><td><label for="Fname">First Name:</label></td>
      <td><input type="text" id="Fname" name="Fname" value="<?php if (!empty($Fname)) echo $Fname; ?>" /><br /></td></tr>
	  <tr><td><label for="Minit">Middle Initial:</label></td>
      <td><input type="text" id="Minit" name="Minit" value="<?php if (!empty($Minit)) echo $Minit; ?>" /><br /></td></tr>
	  <tr><td><label for="Lname">Last Name:</label></td>
      <td><input type="text" id="Lname" name="Lname" value="<?php if (!empty($Lname)) echo $Lname; ?>" /><br /></td></tr>
      <input type="hidden" id="Ssn" name="Ssn" value="<?php echo $Ssn; ?>" /><br />
	  <tr><td class="label">Gender:</td><td><?php if($row['Sex'] == 'M'){ echo 'Male';}else {echo 'Female';}?></td></tr>
	  <tr><td class="label">Date of Birth:</td><td><?php echo $row['Bdate'] ?></td></tr>
	  <tr><td class="label">Address:</td><td><?php echo $row['Address'] ?></td></tr>
	  <tr><td><label for="Salary">Salary:</label></td>
      <td><input type="text" id="Salary" name="Salary" value="<?php if (!empty($Salary)) echo $Salary; ?>" /><br /></td></tr>
	  <tr><td><label for="Super_ssn">Supervisor:</label></td>
      <td><input type="text" id="Super_ssn" name="Super_ssn" value="<?php if (!empty($Super_ssn)) echo $Super_ssn; ?>" /><br /></td></tr>
	  <tr><td><label for="Dno">Department:</label></td>
      <td><input type="text" id="Dno" name="Dno" value="<?php if (!empty($Dno)) echo $Dno; ?>" /><br /></td></tr>
	  <tr><td class="label">Email:</td><td><?php echo $row['Email'] ?> </td></tr>
	  <tr><td class="label">User Name:</td><td><?php echo $row['User_name'] ?> </td></tr>
	  </table>
	  
    </fieldset>
    <input type="submit" value="Save Profile" name="submit" />
  </form>