<?php

require_once ("../constants/verificationStatuses.php");

class User {

    //fields
    private $verifiedStatus;
    private $username;
    private $studentID;

    function __construct ($username, $studentID, $verifiedStatus = VERIFICATION_STATUSES["PENDING"]) {
        $this->setUserName($username);
        $this->setStudentID($studentID);
        $this->setVerifiedStatus($verifiedStatus);

    } //end construct

    /**
    *Get Verified Status
    *@return integer $verifiedStatus
    */
    function getVerifiedStatus () {
        return $this->verifiedStatus;
    }

    /**
    *Set Verified Status
    *@param integer $verifiedStatus
    */
    function setVerifiedStatus ($verifiedStatus) {
        $this->verifiedStatus = $verifiedStatus;
    }

    /**
    *Get Username
    *@return string $username
    */
    function getUsername() {
        return $this->username;
    }

    /**
    *Set Username
    *@param string $username
    */
    function setUsername ($username) {
        $this->username = $username;
    }

    /**
    *Get studentID
    *@return string $studentID
    */
    function getStudentID () {
        return $this->studentID;
    }

    /**
     *Set Student ID
     *@param string $studentID
     */
    function setStudentID ($studentID){
        $this->studentID = $studentID;
    }

    /**
    * Compares two users, username and studentID
    * @param other user
    * @return boolean
    */
    function equals($otherUser) {
        return $otherUser->getUsername() === $this->getUsername() &&
               $otherUser->getStudentID() === $this->getStudentID();
    }

} //End User



?>
