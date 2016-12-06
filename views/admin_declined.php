<?php
  $verificationStatuses = require ('../constants/verificationStatuses.php');
  require("../model/UserDB.php");

  $studentName = $_GET['username'];
  $code = $_GET['code'];
  $db = new UserDB ();

  $db->invalidateUser($studentName, $code);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" type="text/css" href="../style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css" />

    <title>Password Reset Portal</title>

  </head>
  <body class="BG">

    <!-- container  -->
    <div class="container">
        <div class="signin-card">
          <div class="logo-image">
            <img src="http://www.instruction.greenriver.edu/filson/GR_logo_Grn.jpg" alt="Logo" title="Logo" class="img-responsive">
          </div>

            <?php
              echo '<h1 class="userApproval"> ' . $studentName . ' User Declined</h1>';
            ?>

        </div>
      </div>

      <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.js"></script>
    	<script src="main.js"></script>
  </body>
</html>
