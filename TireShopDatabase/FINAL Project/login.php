<?php
  require_once('connectvars.php');

  // Start the session
  session_start();

  // Clear the error message
  $error_msg = "";

  // If the user isn't logged in, try to log them in
  if (!isset($_SESSION['Ssn'])) {
    if (isset($_POST['submit'])) {
      // Connect to the database
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // Grab the user-entered log-in data
      $user_username = mysqli_real_escape_string($dbc, trim($_POST['User_name']));
      $user_password = mysqli_real_escape_string($dbc, trim($_POST['Password']));

      if (!empty($user_username) && !empty($user_password)) {
        // Look up the username and password in the database
        $query = "SELECT Ssn, User_name, Admin_user FROM A_EMPLOYEE WHERE User_name = '$user_username' AND Password = '$user_password'";
        $data = mysqli_query($dbc, $query);

        if (mysqli_num_rows($data) == 1) {
          // The log-in is OK so set the user ID and username session vars (and cookies), and redirect to the home page
          $row = mysqli_fetch_array($data);
          $_SESSION['Ssn'] = $row['Ssn'];
          $_SESSION['User_name'] = $row['User_name'];
		  $_SESSION['Admin_user'] = $row['Admin_user'];
          setcookie('Ssn', $row['Ssn'], time() + (60 * 60 * 24 * 30));    // expires in 30 days
          setcookie('User_name', $row['User_name'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
		  setcookie('Admin_user', $row['Admin_user'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
          $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/companyIndex.php';
          header('Location: ' . $home_url);
        }
        else {
          // The username/password are incorrect so set an error message
          $error_msg = 'Sorry, you must enter a valid username and password to log in.';
        }
      }
      else {
        // The username/password weren't entered so set an error message
        $error_msg = 'Sorry, you must enter your username and password to log in.';
      }
    }
  }

  // Insert the page header
  $page_title = 'Log In';

  // If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
  if (empty($_SESSION['Ssn'])) {
    echo '<p class="error">' . $error_msg . '</p>';
?>

  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
      <legend>Log In</legend>
      <label for="User_name">Username:</label>
      <input type="text" name="User_name" value="<?php if (!empty($user_username)) echo $user_username; ?>" /><br />
      <label for="Password">Password:</label>
      <input type="Password" name="Password" />
    </fieldset>
    <input type="submit" value="Log In" name="submit" />
  </form>

<?php
  }
  else {
    // Confirm the successful log-in
    echo('<p class="login">You are logged in as ' . $_SESSION['User_name'] . '.</p>');
  }
?>
