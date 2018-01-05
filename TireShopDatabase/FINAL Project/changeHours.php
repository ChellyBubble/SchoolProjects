<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Change Employee Hours';
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
  
  if (isset($_GET['Ssn']) && isset($_GET['Lname']) && isset($_GET['Fname']) && isset($_GET['Pno'])) {
    // Grab the score data from the GET
    $Ssn = $_GET['Ssn'];
	$Lname = $_GET['Lname'];
    $Fname = $_GET['Fname'];
    $Pno = $_GET['Pno'];
	
  }
  else if (isset($_POST['Ssn']) && isset($_POST['Lname']) && isset($_POST['Fname']) && isset($_POST['Pno'])) {
    // Grab the score data from the POST
    $Ssn = $_POST['Ssn'];
	$Lname = $_POST['Lname'];
    $Fname = $_POST['Fname'];
    $Pno = $_POST['Pno'];
  }
  else {
    echo '<p class="error">Sorry, no employee was specified for removal.</p>';
  }
  
  if (isset($Ssn) && isset($Lname) && isset($Fname) && isset($Pno)) {
  
	if (isset($_POST['submit'])) {

	  // Connect to the database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 
	  $query = "UPDATE A_WORKS_ON SET Hours = '$Hours' WHERE Essn = '" . $Ssn . "' AND Pno = '" . $Pno . "'";
      mysqli_query($dbc, $query);
      mysqli_close($dbc);

      // Confirm success with the user
      echo '<p> Employee ' . $Fname . ' ' . $Lname . ' from project number ' . $Pno . ' was successfully changed.';
    }
  }
  else {
        echo '<p class="error">You must enter all of the information.</p>';
  }

  $dbc_3 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $query_2 = "SELECT Pname FROM A_PROJECT WHERE Pnumber = '$Pno'";
  $data_2 = mysqli_query($dbc_3, $query_2);
  $row_2 = mysqli_fetch_array($data_2);
  
?>
  <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MM_MAXFILESIZE; ?>" />
    <fieldset>
      <legend>Change Employee Hours</legend>
	  <table>
      <tr><td class="label">First Name:</td><td><?php echo $Fname ?> </td></tr>
	  <tr><td class="label">Last name:</td><td><?php echo $Lname ?></td></tr>
	  <input type="hidden" id="Ssn" name="Ssn" value="<?php echo $Ssn; ?>" />
	  <tr><td class="label">Project Name:</td><td><?php echo $row_2['Pname'] ;?></td></tr>
	  <tr><td><label for="Hours">Hours:</label><br /></td>
	  <td><input type="text" id="Hours" name="Hours" /></td></tr>
	  </table>
	  
    </fieldset>
    <input type="submit" value="Save Hours" name="submit" />
  </form>

</body> 
</html>