<?php
// Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'E-mail Employees';
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
    $from = 'crazychelsea3600@gmail.com';
    $subject = $_POST['subject'];
    $text = $_POST['body'];
    $output_form = false;

    if (empty($subject) && empty($text)) {
      // We know both $subject AND $text are blank 
      echo 'You forgot the email subject and body text.<br />';
      $output_form = true;
    }

    if (empty($subject) && (!empty($text))) {
      echo 'You forgot the email subject.<br />';
      $output_form = true;
    }

    if ((!empty($subject)) && empty($text)) {
      echo 'You forgot the email body text.<br />';
      $output_form = true;
    }
  }
  else {
    $output_form = true;
  }

  if ((!empty($subject)) && (!empty($text))) {
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
      or die('Error connecting to MySQL server.');

    $query = "SELECT * FROM A_EMPLOYEE";
    $result = mysqli_query($dbc, $query)
      or die('Error querying database.');

    while ($row = mysqli_fetch_array($result)){
      $to = $row['Email'];
      $Fname = $row['Fname'];
      $Lname = $row['Lname'];
      $msg = "Dear $Fname $Lname,\n$text";
      mail($to, $subject, $msg, 'From:' . $from);
      echo 'Email sent to ' . $Fname . ' '. $Lname . '<br />';
    } 

    mysqli_close($dbc);
  }

  if ($output_form) {
?>

  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <label for="subject">Subject of E-mail:</label><br />
    <input id="subject" name="subject" size="30" type="text" value="<?php echo $subject; ?>" /><br />
    <label for="body">Body of E-mail:</label><br />
    <textarea id="body" name="body" rows="8" cols="40" value="<?php echo $text; ?>"></textarea><br />
    <input type="submit" name="submit" value="Submit" /><br />
  </form>

<?php
  }
?>

</body>
</html>
