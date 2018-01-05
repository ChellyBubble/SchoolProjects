<?php
  // Start the session  
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'New Employee';
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
  
  if (isset($_POST['submit'])) {
	$Fname = $_POST['Fname'];
	$Minit = $_POST['Minit'];
	$Lname = $_POST['Lname'];
	$Ssn = $_POST['Ssn'];
	$Bdate = $_POST['Bdate'];
	$Address = $_POST['Address'];
	$Sex = $_POST['Sex'];
	$Salary = $_POST['Salary'];
	$Super_ssn = $_POST['Super_ssn'];
	$Dno = $_POST['Dno'];
	$Email = $_POST['Email'];
	$User_name = $_POST['User_name'];
	$output_form = 'no';
  
    if (empty($Fname) || empty($Minit) || empty($Lname) || empty($Ssn) ||empty($Bdate) || empty($Address) || empty($Sex) || empty($Salary) || empty($Dno) || empty($Email) || empty($User_name)) {
      // We know at least one of the input fields is blank 
      echo 'Please fill out all of the email information.<br />';
      $output_form = 'yes';
    }
  }
  else {
    $output_form = 'yes';
  }
  
  if (!empty($Fname) && !empty($Minit) && !empty($Lname) && !empty($Ssn) && !empty($Bdate) && !empty($Address) && !empty($Sex) && !empty($Salary) && !empty($Dno) && !empty($Email) && !empty($User_name)) {
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
		or die('Error connecting to MySQL server.');
  
	if(!empty($Super_ssn) ){
		$query = "INSERT INTO A_EMPLOYEE (Fname, Minit, Lname, Ssn, Bdate, Address, Sex, Salary, Super_ssn, Dno, Email, User_name)  VALUES ('$Fname','$Minit', '$Lname', '$Ssn', '$Bdate', '$Address','$Sex','$Salary','$Super_ssn', '$Dno', '$Email', '$User_name')";
		mysqli_query($dbc, $query)
			or die('Error querying database.');

		echo 'Employee added.';
	}
	else{
		$query_2 = "INSERT INTO A_EMPLOYEE (Fname, Minit, Lname, Ssn, Bdate, Address, Sex, Salary, Dno, Email, User_name) VALUES ('$Fname','$Minit', '$Lname', '$Ssn', '$Bdate', '$Address','$Sex','$Salary', '$Dno', '$Email', '$User_name')";
		mysqli_query($dbc, $query_2)
			or die('Error querying database.');

		echo 'Employee added.';
	}
	
	mysqli_close($dbc);
  }
  
  if($output_form == 'yes') {
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<table>
		<tr><td><label for="Fname">First Name:</label></td>
		<td><input type="text" id="Fname" name="Fname" /></td></tr>
		<tr><td><label for="Minit">Middle Initial:</label></td>
		<td><input type="text" id="Minit" name="Minit" /></td></tr>
		<tr><td><label for="Lname">Last Name:</label></td>
		<td><input type="text" id="Lname" name="Lname" /></td></tr>
		<tr><td><label for="Ssn">SSN:</label></td>
		<td><input type="text" id="Ssn" name="Ssn" /></td></tr>
		<tr><td><label for="Bdate">DOB:</label></td>
		<td><input type="text" id="Bdate" name="Bdate" /></td></tr>
		<tr><td><label for="Address">Address:</label></td>
		<td><input type="text" id="Address" name="Address" /></td></tr>
		<tr><td><label for="Sex">Gender:</label></td>
		<td><select id="Sex" name="Sex" />
		<option value = "M" >Male</option><br />
		<option value = "F" >Female</option><br />
		</td></tr>
		<tr><td><label for="Salary">Salary:</label></td>
		<td><input type="text" id="Salary" name="Salary" /></td></tr>
		<tr><td><label for="Super_ssn">Supervisor:</label></td>
	    <td><select id = "Super_ssn" name = "Super_ssn" />
	    <?php
		$dbc_op = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
			or die('Error connecting to MySQL server.');
		$query_op = "SELECT * FROM A_EMPLOYEE WHERE Admin_user = 1 ORDER BY Lname";
		$data = mysqli_query($dbc_op, $query_op);
		while($row = mysqli_fetch_array($data)){
			echo '<option value= "' . $row['Ssn'] . '" >' . $row['Fname'] . ' '. $row['Lname'] . '</option><br />';
		}	
		echo '<option value= "' . NULL . '" >N/A</option><br />';
	    ?>
		</td></tr>
		<tr><td><label for="Dno">Department:</label></td>
	    <td><select id = "Dno" name = "Dno" />
	    <?php
		$dbc_3 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
			or die('Error connecting to MySQL server.');
		$query_3 = "SELECT * FROM A_DEPARTMENT ORDER BY Dname";
		$data = mysqli_query($dbc_3, $query_3);
		while($row = mysqli_fetch_array($data)){
			echo '<option value= "' . $row['Dnumber'] . '" >' . $row['Dname'] . '</option><br />';
		}	
	    ?>
		</td></tr>
		<tr><td><label for="Email">Email:</label></td>
		<td><input type="text" id="Email" name="Email" /></td></tr>
		<tr><td><label for="User_name">User Name:</label></td>
		<td><input type="text" id="User_name" name="User_name" /></td></tr>
		</table>
	</fieldset><br />
		<input type="submit" name="submit" value="Add Employee" />
  </form>

<?php
	}
?>

</body>
</html>