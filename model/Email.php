<?php

  $verificationStatuses = require ('../constants/verificationStatuses.php');
  require_once('../utils/PasswordGenerator.php');

  /**
   * This class manages all business logic associated with email.
   * @author Bogdan Pshonyak
   */
  class Email {

    const CURRENT_WEBSITE_URL = 'http://portal.greenrivertech.net';
    const PASSWORD_LENGTH = 12;

    function __construct() {
      // Do nothing...
    }

    /**
     * Generates the password reset email body
     * @param String $username A tech domain username
     * @param Integer $studentID A student id number
     * @param String $email The recipients email
     * @return String $content Email body in HTML format
     */
    private static function getHTMLEmailBody($user, $email) {

      $username = $user->getUsername();
      $studentID = $user->getStudentID();

      $newPassword = PasswordGenerator::generatePassword(self::PASSWORD_LENGTH);

      $statusAsInteger = $user->getVerifiedStatus();
      $statusAsStringConstant = self::getVerifiedStatusAsString($statusAsInteger);
      $statusColor = self::getVerifiedStatusColor($statusAsInteger);

      $approveLink = self::getApprovedVerificationCodeLink($user);
      $declineLink = self::getDeclinedVerificationCodeLink($user);

      $script = self::getPowerShellResetScript($username, $newPassword, $email);

      $content = "<html>
                      <head>
                          <title>Tech Domain Password Reset</title>
                      </head>
                      <body>
                          <h1>Tech Domain Password Reset</h1>
                          <h2 style='color: {$statusColor}'>Verification Status: <b>{$statusAsStringConstant}</b></h2>
                          <h2>Username:   {$username}</h2>
                          <h2>Student ID: {$studentID}</h2>
                          <h2>Email:      {$email}</h2>
                          <br/>
                          <h2>-- Change Users Verification Status ---</h2>
                          <h3><a href='{$approveLink}'>Approve</a></h3>
                          <h3><a href='{$declineLink}'>Decline</a></h3>
                          <br/>
                          <h1>Powershell Reset Script</h1>
                          <code>
                          	{$script}
                          </code>
                      </body>
                  </html>";

      return $content;
    }

    /**
     * Generates string constant associated with verification status
     * @param String $statusAsInteger verification status as an integer
     * @return String constant associated with verification status
     */
    private static function getVerifiedStatusAsString($statusAsInteger) {
      switch ($statusAsInteger) {
        case -1:
          return 'DECLINED';
          break;
          case 1:
            return 'APPROVED';
            break;
        default:
          return 'PENDING';
          break;
      }
    }

    /**
     * Generates color associated with verification status
     * @param String $statusAsInteger verification status as an integer
     * @return String color associated with verification status
     */
    private static function getVerifiedStatusColor($statusAsInteger) {
      switch ($statusAsInteger) {
        case -1:
          return 'red';
          break;
          case 1:
            return 'green';
            break;
        default:
          return 'orange';
          break;
      }
    }

    /**
     * Generates the password reset script
     * @param String $username A tech domain username
     * @param String $email The recipients email
     * @return String $script PowerShell password reset script
     */
    private static function getPowerShellResetScript($username, $newPassword, $email) {
      $script = '
                #Author: Organized Anarchy
                #Date:   10/10/2016
                #
                #This script resets a student\'s password and sends them an email with
                #instructions on how to sign in.

                #User that needs a password reset.
                $TechDomainUserName = "' . $username . '"

                #Let the new password be "Password01"
                $newpass = "' . $newPassword . '"

                #DO NOT enforce a password change on next logon
                $changePwdAtLogon = $false

                #Set the particular account identified by $TechDomainUserName
                #   to the above password.
                Set-ADAccountPassword -Identity $TechDomainUserName -Reset `
                    -NewPassword (ConvertTo-SecureString -AsPlainText $newpass -Force)

                # Make it so that the user has to change password at next logon
                Set-ADUser $TechDomainUserName -changepasswordatlogon $changePwdAtLogon

                #email contents.
                $to = "' . $email . '"
                $subject = "PowerShell Test Email"
                $body = "Your GRC Tech Domain password has been reset to ' . $newPassword . '"

                #Location of the password in SecureString form.
                $passFile = "C:\Users\kdevan\grctechportal-gmail-password.txt"

                #Get password out of the file in SecureString form.
                $secPassword = (Get-Content $passFile | ConvertTo-SecureString)

                #Server information.
                $from = "grctechportal@gmail.com"
                $smtpServer = "smtp.gmail.com"
                $smtpPort = "587"

                #Gmail account credential.
                $myCredential=New-Object -TypeName System.Management.Automation.PSCredential `
                 -ArgumentList $from, $secPassword

                #Actually sends the email.
                Send-MailMessage -From $from -To $to -Subject $subject `
                -Body $body -SmtpServer $smtpServer -Port $smtpPort -UseSsl `
                -Credential $myCredential
                ';

        return $script;
    }

    /**
     * Generates email headers
     * @return String $headers
     */
    private static function getEmailHeaders() {
      $headers = "MIME-Version: 1.0" . "\r\n";
    	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    	$headers .= 'From: <no-reply@portal.greenrivertech.net>' . "\r\n";

      return $headers;
    }

    /**
     * Gets the approved verification link for the user. This will be sent as a link
     * in the email so the admin can verify the user.
     * @param String $user The user to generate a link for.
     * @return String A link to approve the given user.
     */
    private static function getApprovedVerificationCodeLink($user) {
      $link = self::CURRENT_WEBSITE_URL;
      $link = $link . '/views/admin_approved.php';

      $link = self::getLinkVariablesAndUpdateVerificationCode($user, $link);

      return $link;
    }

    /**
     * Gets the declined verification link for the user. This will be sent as a link
     * in the email so the admin can decline the user.
     * @param String $user The user to generate a link for.
     * @return String A link to decline the given user.
     */
    private static function getDeclinedVerificationCodeLink($user) {
      $link = self::CURRENT_WEBSITE_URL;
      $link = $link . '/views/admin_declined.php';

      $link = self::getLinkVariablesAndUpdateVerificationCode($user, $link);

      return $link;
    }

    /**
     * Adds variables to the given link to be used when verifying a user on the admin pages.
     * Also updates the user's verification code in the database.
     */
    private static function getLinkVariablesAndUpdateVerificationCode($user, $link) {
      $link = $link . '?username=' . $user->getUsername();

      $code = UserDB::createNewVerificationCode();
      $db = new UserDB();
      $db->updateVerificationCode($user, $code);
      $link = $link . '&code=' . $code;

      return $link;
    }

    /**
     * Generates the password reset script
     * @param String $username A tech domain username
     * @param String $email The recipients email
     * @return Boolean True if the mail operation executed without errors
     */
    public static function sendPasswordResetEmail($user, $email) {
    	$to = "grctechportal@gmail.com";
    	$subject = "Tech Domain Password Reset";

    	$content = self::getHTMLEmailBody($user, $email);
    	$headers = self::getEmailHeaders();

    	return mail($to,$subject,$content,$headers);
    }

  }

 ?>
