<?php
  // Generate the navigation menu
  echo '<hr />';
  if (isset($_SESSION['User_name'])) {
	if ($_SESSION['Admin_user'] == '0'){
		echo '<a href="companyIndex.php">Home</a> &#9728; ';
		echo '<a href="employeeInfo.php">Personal Information</a> &#9728; ';
		echo '<a href="addProject.php">New Project</a> &#9728; ';	
		echo '<a href="logout.php">Log Out (' . $_SESSION['User_name'] . ')</a>';
	}
	else{
		echo '<a href="companyIndex.php">Home</a> &#9728; ';
		echo '<a href="employeeInfo.php">Personal Information</a> &#9728; ';
		echo '<a href="adminProjects.php">Projects</a> &#9728; ';
		echo '<a href="adminEmployees.php">Employees</a> &#9728; ';
		echo '<a href="adminEmployeeHours.php">Employee Hours</a> &#9728; ';
		echo '<a href="emailEmployees.php">E-mail Employees</a> &#9728; ';
		echo '<a href="report.php">Report</a> &#9728; ';
		echo '<a href="logout.php">Log Out (' . $_SESSION['User_name'] . ')</a>';
	}
  }
  else {
    echo '<a href="login.php">Log In</a> &#9728; ';
  }
  echo '<hr />';
?>