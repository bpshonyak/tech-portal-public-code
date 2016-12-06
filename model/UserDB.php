<?php

require_once ("../constants/verificationStatuses.php");
require_once ("EncryptedUser.php");

class UserDB {
  //fields
  private $db;

  function __construct() {

    $this->db = require('../../database/pdo-cnxn.php');
  }

  /**
   *Get User from database
   *@param string $username A valid username
   *@return User user Object
   */
  function getUser($username) {
    $sql = "SELECT username, student_id, verified_status FROM tech_users WHERE username = :username";
    $query = $this->db->prepare($sql);

    $query->bindValue("username", $username, PDO::PARAM_STR);

    $query->execute();
    $user = $query->fetch();

    if ($user == NULL ) {
      return NULL;
    }

    return $this->createUserFromDB($user);
  }

  private function createUserFromDB($userInfo) {
    return new EncryptedUser($userInfo['username'], $userInfo['student_id'], $userInfo['verified_status']);
  }

  /**
   *Insert User into the database
   *@param User $user Valid user Object
   *@return true is query executed successfully
   */
  function insertUser(User $user) {
    $encryptedUser = EncryptedUser::getEncrpytedUserFromUser($user);

    $sql = "INSERT INTO tech_users (username, student_id)
    VALUES (:username, :student_id)";
    $query = $this->db->prepare($sql);

    $query->bindValue("username", $encryptedUser->getUsername(), PDO::PARAM_STR);
    $query->bindValue("student_id", $encryptedUser->getStudentID(), PDO::PARAM_STR);

    return ($query->execute());
  }

  /**
   *Sets a user's verified_status to APPROVED so they can reset their
   *password automatically in the future.
   *@param String $username A valid username
   *@return true is query executed successfully
   */
  function validateUser($username) {
    $sql = "UPDATE tech_users SET verified_status=:verified_status" .
    " WHERE username=:username";

    $query = $this->db->prepare($sql);

    $query->bindValue("verified_status", VERIFICATION_STATUSES["APPROVED"], PDO::PARAM_INT);
    $query->bindValue("username", $username, PDO::PARAM_STR);

    return ($query->execute());
  }

  /**
   *Sets a user's verified_status to DECLINED so they cannot reset their
   *password automatically in the future.
   *@param string $username A valid username
   *@return true is query executed successfully
   */
  function invalidateUser($username) {
    $sql = "UPDATE tech_users SET verified_status=:verified_status" .
    " WHERE username=:username";

    $query = $this->db->prepare($sql);

    $query->bindValue("verified_status", VERIFICATION_STATUSES["DECLINED"], PDO::PARAM_INT);
    $query->bindValue("username", $username, PDO::PARAM_STR);

    return ($query->execute());
  }

  /**
   *Assign a new verification code to the user. This code should expire
   *after some period of time.
   *@param User $user valid user Object
   *@param String $verificationCode valid verification code
   */
  function updateVerificationCode($user, $verificationCode) {

    $sql = "UPDATE tech_users SET verification_code=:verification_code" .
    " WHERE username=:username";

    $query = $this->db->prepare($sql);

    $query->bindValue("verification_code", $verificationCode, PDO::PARAM_STR);
    $query->bindValue("username", $user->getUsername(), PDO::PARAM_STR);

    return ($query->execute());
  }

  /**
   *Create a new verification code.
   *@return String random characters
   */
  static function createNewVerificationCode() {
      $uniqueString = uniqid(null, true); //generate a long unique id
      $hashedString = md5($uniqueString); //generate hash
      $finalSubString = substr($hashedString, 0, 20); //save the first 20 characters
      return $finalSubString;
  }

}



?>
