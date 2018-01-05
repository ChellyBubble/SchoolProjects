<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Administration Employees Hours';
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
  
  if (isset($_GET['Ssn']) && isset($_GET['Lname']) && isset($_GET['Fname'])) {
    // Grab the score data from the GET
    $Ssn = $_GET['Ssn'];
	$Lname = $_GET['Lname'];
    $Fname = $_GET['Fname'];
  }
  else if (isset($_POST['Ssn']) && isset($_POST['Lname']) && isset($_POST['Fname'])) {
    // Grab the score data from the POST
    $Ssn = $_POST['Ssn'];
	$Lname = $_POST['Lname'];
    $Fname = $_POST['Fname'];
  }
  
  else {
    echo '<p class="error">Sorry, no employee was specified for removal.</p>';
  }
  // Connect to the database 
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  
  // Retrieve the score data from MySQL
  $query = "SELECT * FROM A_WORKS_ON WHERE Essn = $Ssn ORDER BY Pno";
  $data = mysqli_query($dbc, $query);

  // Loop through the array of score data, formatting it as HTML 
  echo '<fieldset>';
  echo '<legend>' . $Fname . ' ' . $Lname . ' Logged Hours</legend>';
  echo '<table>';
  echo '<tr><th>Project No.</th><th>Project Name</th><th>Hours</th><th>Action</th></tr>';
  while ($row = mysqli_fetch_array($data)) { 
  
	$query_3 = "SELECT Pname  FROM A_PROJECT WHERE Pnumber = '" . $row['Pno'] . "'";
	$data_3 = mysqli_query($dbc, $query_3);
	$row_3 = mysqli_fetch_array($data_3);

    echo '<tr class="important"><td><strong>' . $row['Pno'] . '</strong></td>';
	echo '<td>' . $row_3['Pname'] . '</td>';
	echo '<td>' . $row['Hours'] . '</td>';
	echo '<td><a href="changeHours.php?Ssn=' . $Ssn . '&amp;Lname=' . $Lname .
      '&amp;Fname=' . $Fname . '&amp;Pno=' . $row['Pno'] . '">Change</a>';
	echo ' / ';
    echo '<td><a href="removeHours.php?Ssn=' . $Ssn . '&amp;Lname=' . $Lname .
      '&amp;Fname=' . $Fname . '&amp;Pno=' . $row['Pno'] . '">Remove</a>';
  }
  echo '</table>';
  echo '</fieldset>';

  echo '<p><a href="addHours.php?Ssn=' . $Ssn . '&amp;Lname=' . $Lname . '&amp;Fname=' . $Fname . '">Add New Project Hours</a></p>';

?>


</body> 
</html>