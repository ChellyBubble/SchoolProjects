<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Remove Employee';
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
  
  if (isset($_GET['Ssn']) && isset($_GET['Lname']) && isset($_GET['Fname']) && isset($_GET['Dno'])) {
    // Grab the score data from the GET
    $Ssn = $_GET['Ssn'];
	$Lname = $_GET['Lname'];
    $Fname = $_GET['Fname'];
    $Dno = $_GET['Dno'];
	
  }
  else if (isset($_POST['Ssn']) && isset($_POST['Lname']) && isset($_POST['Fname']) && isset($_POST['Dno'])) {
    // Grab the score data from the POST
    $Ssn = $_POST['Ssn'];
	$Lname = $_POST['Lname'];
    $Fname = $_POST['Fname'];
    $Dno = $_POST['Dno'];
  }
  else {
    echo '<p class="error">Sorry, no employee was specified for removal.</p>';
  }

  if (isset($_POST['submit'])) {
    if ($_POST['confirm'] == 'Yes') {
      
	  // Connect to the database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 

      // Delete the score data from the database
      $query = "DELETE FROM A_EMPLOYEE WHERE Ssn = $Ssn LIMIT 1";
      mysqli_query($dbc, $query);
      mysqli_close($dbc);

      // Confirm success with the user
      echo '<p> Employee ' . $Fname . ' ' . $Lname . ' from department number ' . $Dno . ' was successfully removed.';
    }
    else {
      echo '<p class="error">The employee was not removed.</p>';
    }
  }
  else if (isset($Ssn) && isset($Lname) && isset($Fname) && isset($Dno)) {
	
    echo '<p>Are you sure you want to delete the following employee?</p>';
    echo '<p><strong>Name: </strong>' . $Fname . ' ' . $Lname . '<br /><strong>Dno: </strong>' . $Dno .
      '<br /> </p>';
    echo '<form method="post" action="removeEmployee.php">';
    echo '<input type="radio" name="confirm" value="Yes" /> Yes ';
    echo '<input type="radio" name="confirm" value="No" checked="checked" /> No <br />';
    echo '<input type="submit" value="Submit" name="submit" />';
    echo '<input type="hidden" name="Ssn" value="' . $Ssn . '" />';
    echo '<input type="hidden" name="Lname" value="' . $Lname . '" />';
    echo '<input type="hidden" name="Fname" value="' . $Fname . '" />';
	echo '<input type="hidden" name="Dno" value="' . $Dno . '" />';
    echo '</form>';
  }

?>

</body> 
</html>