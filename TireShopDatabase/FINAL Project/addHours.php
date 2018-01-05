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
	$Pno = mysqli_real_escape_string($dbc, trim($_POST['Pno']));
	$Hours = mysqli_real_escape_string($dbc, trim($_POST['Hours']));

    $error = false;

    // Update the profile data in the database
    if (!$error) {
      if (!empty($Pno) && !empty($Hours) && !empty($Ssn)) {
        
        $query = "INSERT INTO A_WORKS_ON (Essn, Pno, Hours)  VALUES ('$Ssn','$Pno', '$Hours')";
			
		mysqli_query($dbc, $query)
			or die('Error querying database.');

		// Confirm success with the user
		echo '<p>Employee hours has been successfully updated. </p>';
		
        mysqli_close($dbc);
        exit();
		
      }
      else {
        echo '<p class="error">You must enter all of the information.</p>';
      }
    }
  } // End of check for form submission

  mysqli_close($dbc);
?>

  <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MM_MAXFILESIZE; ?>" />
    <fieldset>
      <legend>Employee Hour Information</legend>
	  <table>
      <tr><td class="label">First Name:</td><td><?php echo $Fname ?> </td></tr>
	  <tr><td class="label">Last name:</td><td><?php echo $Lname ?></td></tr>
	  <input type="hidden" id="Ssn" name="Ssn" value="<?php echo $Ssn; ?>" /><br />
	  <tr><td><label for="Pno">Project Name:</label></td>
	  <td><select id = "Pno" name = "Pno">
	  <?php
		$dbc_op = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
			or die('Error connecting to MySQL server.');
		$query_op = "SELECT * FROM A_PROJECT ORDER BY Pnumber";
		$data = mysqli_query($dbc_op, $query_op);
		while($row = mysqli_fetch_array($data)){
			echo '<option value= "' . $row['Pnumber'] . '" >' . $row['Pname'] . '</option><br />';
		}	
	  ?>
	  </td></tr>
	  <tr><td><label for="Hours">Hours:</label><br /></td>
	  <td><input type="text" id="Hours" name="Hours" /><br /></td></tr>
	  </table>
	  
    </fieldset>
    <input type="submit" value="Save Hours" name="submit" />
  </form>