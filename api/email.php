<?php
require_once ("../constants/verificationStatuses.php");
require ("../model/User.php");
require ("../model/UserDB.php");
require ("../model/Email.php");

//The switch chooses what server Request_Method is being submitted
SWITCH ($_SERVER["REQUEST_METHOD"]) {

    case "GET":
        break;

    case "POST":

        $username = $_POST["username"];
        $studentID = $_POST["studentID"];
        $email = $_POST["email"];

        $userDB = new UserDB();

        $userFromParams = new User($username, $studentID);
        $userFromDB = $userDB->getUser($username);

        if ($userFromDB == NULL) {
            $newUser = new User($username, $studentID);
            $userDB->insertUser($newUser);
            Email::sendPasswordResetEmail($newUser, $email);
            $result = $_POST;
        } else {
            //compare user input with database user information
            $encryptedUserFromParams = EncryptedUser::getEncrpytedUserFromUser($userFromParams);
            if(!($encryptedUserFromParams->equals($userFromDB))) {
                 $result = array('error' => 401 , 'msg' => 'You have provided invalid information!' );
            } else {
                $verificationStatus = $userFromDB->getVerifiedStatus();

                if ($verificationStatus == VERIFICATION_STATUSES["DECLINED"]) {
                  $result = array('error' => 401 , 'msg' => 'Your request has been denied! Please email kmahadevan@greenriver.edu for assistance.' );
                } else {
                    Email::sendPasswordResetEmail($userFromParams, $email);
                    $result = $_POST;
                }
            }
        }

        break;

    case "PUT":
        break;

    case "DELETE":
    	break;

}

header("Content-Type: application/json");
echo json_encode($result);
