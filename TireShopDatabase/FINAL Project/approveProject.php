<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Approve Project';
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
  
  if (isset($_GET['Pnumber']) && isset($_GET['Pname']) && isset($_GET['Dnum']) && isset($_GET['Plocation'])) {
    // Grab the score data from the GET
    $Pnumber = $_GET['Pnumber'];
	$Pname = $_GET['Pname'];
    $Dnum = $_GET['Dnum'];
    $Plocation = $_GET['Plocation'];
  }
  else if (isset($_POST['Pnumber']) && isset($_POST['Pname']) && isset($_POST['Dnum']) && isset($_POST['Plocation'])) {
    // Grab the score data from the POST
    $Pnumber = $_POST['Pnumber'];
	$Pname = $_POST['Pname'];
    $Dnum = $_POST['Dnum'];
    $Plocation = $_POST['Plocation'];
  }
  else {
    echo '<p class="error">Sorry, no project was specified for approval.</p>';
  }
  
  if (isset($_POST['submit'])) {
    if ($_POST['confirm'] == 'Yes') {
	  
	  // Connect to the database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 

      //Approve the project from the database
      $query = "UPDATE A_PROJECT SET Papproved = 1 WHERE Pnumber = $Pnumber";
      mysqli_query($dbc, $query);
      mysqli_close($dbc);

      // Confirm success with the user
      echo '<p> Project ' . $Pname . ' from department number ' . $Dnum . ' was successfully approved.';
    }
    else {
      echo '<p class="error">The project was not approved.</p>';
    }
  }
  else if (isset($Pnumber) && isset($Pname) && isset($Dnum) && isset($Plocation)) {
	
    echo '<p>Are you sure you want to approve the following project?</p>';
    echo '<p><strong>Project Name: </strong>' . $Pname . '<br /><strong>Department Number: </strong>' . $Dnum .
      '<br /> </p>';
    echo '<form method="post" action="approveProject.php">';
    echo '<input type="radio" name="confirm" value="Yes" /> Yes ';
    echo '<input type="radio" name="confirm" value="No" checked="checked" /> No <br />';
    echo '<input type="submit" value="Submit" name="submit" />';
    echo '<input type="hidden" name="Pnumber" value="' . $Pnumber . '" />';
    echo '<input type="hidden" name="Pname" value="' . $Pname . '" />';
    echo '<input type="hidden" name="Dnum" value="' . $Dnum . '" />';
	echo '<input type="hidden" name="Plocation" value="' . $Plocation . '" />';
    echo '</form>';
  }

?>

</body> 
</html>