<?php
use PHPUnit\Framework\TestCase;

require_once ("../model/Email.php");
require_once ('../model/UserDB.php');
require_once ('../model/User.php');

/**
 * Class EmailTest
 *
 * Runs tests for the Email class.
 */
class EmailTest extends TestCase {

    /**
    * Asserts that the sendPasswordResetEmail function returns true for a valid
    * user and email address.
    */
    public function testSendingEmail() {

        $user = new User('TestUser', '5555555555');
        $email = 'TestUser@testmail.com';

        $result = Email::sendPasswordResetEmail($user, $email);

        $this->assertEquals(true, $result);
    }

}
