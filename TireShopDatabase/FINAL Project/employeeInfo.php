<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Personal Information';
  require_once('header.php');

  require_once('appvars.php');
  require_once('connectvars.php');

  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['Ssn'])) {
    echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
    exit();
  }

  // Show the navigation menu
  require_once('navMenu.php');

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  // Grab the profile data from the database
  if (!isset($_GET['Ssn'])) {
    $query = "SELECT *  FROM A_EMPLOYEE WHERE Ssn = '" . $_SESSION['Ssn'] . "'";
  }
  else {
    $query = "SELECT * FROM A_EMPLOYEE WHERE Ssn = '" . $_GET['Ssn'] . "'";
  }
  $data = mysqli_query($dbc, $query);

  if (mysqli_num_rows($data) == 1) {
    
	if (!isset($_GET['Ssn']) || ($_SESSION['Ssn'] == $_GET['Ssn'])) {
        
		// Show the users Name and Sex
		$row = mysqli_fetch_array($data);
		echo '<table>';
		if (!empty($row['Fname'])) {
			echo '<tr><td class="label">First Name:</td><td>' . $row['Fname'] . '</td></tr>';
		}
		if (!empty($row['Minit'])) {
			echo '<tr><td class="label">Middle Initial:</td><td>' . $row['Minit'] . '</td></tr>';
		}
		if (!empty($row['Lname'])) {
			echo '<tr><td class="label">Last name:</td><td>' . $row['Lname'] . '</td></tr>';
		}
		if (!empty($row['Sex'])) {
			echo '<tr><td class="label">Gender:</td><td>';
		if ($row['Sex'] == 'M') {
			echo 'Male';
		}
		else if ($row['Sex'] == 'F') {
			echo 'Female';
		}
		else {
			echo '?';
		}
		echo '</td></tr>';
		}
		
		// Show the user their own birthdate
        if (!empty($row['Bdate'])) {
			echo '<tr><td class="label">Date of Birth:</td><td>' . $row['Bdate'] . '</td></tr>';
		}
		//Show the user their own address
		if (!empty($row['Address'])) {
			echo '<tr><td class="label">Address:</td><td>' . $row['Address'] . '</td></tr>';
		}
		// Show the user their Salary
        if (!empty($row['Salary'])) {
			echo '<tr><td class="label">Salary:</td><td>' . $row['Salary'] . '</td></tr>';
		}
		//Show the user their Supervisor
		$query_2 = "SELECT Fname, Lname  FROM A_EMPLOYEE WHERE Ssn = '" . $row['Super_ssn'] . "'";
		$data_2 = mysqli_query($dbc, $query_2);
		$row_2 = mysqli_fetch_array($data_2);
			
		if (!empty($row_2['Fname']) && !empty($row_2['Lname'])) {
			echo '<tr><td class="label">Supervisor:</td><td>' . $row_2['Fname'] . ' ' . $row_2['Lname'] . '</td></tr>';
		}
		
		// Show the user their own Department
		$query_3 = "SELECT Dname  FROM A_DEPARTMENT WHERE Dnumber = '" . $row['Dno'] . "'";
		$data_3 = mysqli_query($dbc, $query_3);
		$row_3 = mysqli_fetch_array($data_3);
			
		if (!empty($row_3['Dname'])) {
			echo '<tr><td class="label">Department:</td><td>' . $row_3['Dname'] . '</td></tr>';
		}

		//Show the user their Email
		if (!empty($row['Email'])) {
			echo '<tr><td class="label">Email:</td><td>' . $row['Email'] . '</td></tr>';
		}
		// Show the user their User_name
        if (!empty($row['User_name'])) {
			echo '<tr><td class="label">User Name:</td><td>' . $row['User_name'] . '</td></tr>';
		}
		
		if (!empty($row['Picture'])) {
			echo '<tr><td class="label">Picture:</td><td><img src="' . MM_UPLOADPATH . $row['Picture'] .
        '" alt="Profile Picture" /></td></tr>';
		}
	}
	
    echo '</table>';

  } // End of check for a single row of user results
  else {
    echo '<p class="error">There was a problem accessing your information.</p>';
  }

  mysqli_close($dbc);
?>

  <p><a href="editEmployeeInfo.php">Edit Personal Information</a></p>
