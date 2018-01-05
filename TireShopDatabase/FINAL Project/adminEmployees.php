<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Administration Employees';
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

  // Retrieve the score data from MySQL
  $query = "SELECT * FROM A_EMPLOYEE ORDER BY Dno, Lname";
  $data = mysqli_query($dbc, $query);

  // Loop through the array of score data, formatting it as HTML 
  echo '<table>';
  echo '<tr><th>Last Name</th><th>First Name</th><th>Department</th><th>Action</th></tr>';
  while ($row = mysqli_fetch_array($data)) { 
    // Display the score data
	// Show the user their own Department
	$query_3 = "SELECT Dname  FROM A_DEPARTMENT WHERE Dnumber = '" . $row['Dno'] . "'";
	$data_3 = mysqli_query($dbc, $query_3);
	$row_3 = mysqli_fetch_array($data_3);

    echo '<tr class="important"><td><strong>' . $row['Lname'] . '</strong></td>';
	echo '<td>' . $row['Fname'] . '</td>';
    echo '<td>' . $row_3['Dname'] . '</td>';
    echo '<td><a href="removeEmployee.php?Ssn=' . $row['Ssn'] . '&amp;Lname=' . $row['Lname'] .
      '&amp;Fname=' . $row['Fname'] . '&amp;Dno=' . $row['Dno'] . '">Remove</a>';
	echo ' / ';
	echo '<td><a href="modifyEmployee.php?Ssn=' . $row['Ssn'] . '&amp;Lname=' . $row['Lname'] .
      '&amp;Fname=' . $row['Fname'] . '&amp;Dno=' . $row['Dno'] . '">Modify</a>';
    echo '</td></tr>';
  }
  echo '</table>';

  mysqli_close($dbc);
?>
  <p><a href="addEmployee.php">Add New Employee</a></p>
</body> 
</html>