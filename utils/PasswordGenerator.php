<?php
/*
Organized Anarchy
10.28.2016
passwordGenerator.php
*/


class PasswordGenerator {

  const MINIMUM_REQUIRED_CHARACTERS = 4;
  const LOWER_ALPHABET = "abcdefghijklmnopqrstuvwxyz";
  const UPPER_ALPHABET = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  const NUMBERS = "0123456789";
  const SPECIAL_CHARACTERS = "#@!&*^%";

  /**
   * Returns a randomly generated password of the given length
   * that has at minimum four characters. This password is guaranteed
   * to include an Uppercase letter, a Lowercase letter, a Number, and
   * a Special character (#@!&*^%)
   * @param $length the length of the desired password
   * @return string the generated password
   */
  public static function generatePassword ($length) {

    $password = self::getBasePassword();

    $characters = self::LOWER_ALPHABET . self::UPPER_ALPHABET . self::NUMBERS . self::SPECIAL_CHARACTERS;

    $len = strlen($characters);

    for ($i = 0; $i<$length - self::MINIMUM_REQUIRED_CHARACTERS; $i++) {
      $password .= substr($characters, rand(0, $len-1), 1);
    }

    // the finished password
    $password = str_shuffle($password);

    return $password;

  }

  /**
   * Returns an initial password that is guaranteed to include
   * a character from each of the major categories.
   * @return string the generated initial password
   */
  private static function getBasePassword(){
    $password = "";

    $password .= substr(self::LOWER_ALPHABET, rand(0, strlen(self::LOWER_ALPHABET)-1), 1);
    $password .= substr(self::UPPER_ALPHABET, rand(0, strlen(self::UPPER_ALPHABET)-1), 1);
    $password .= substr(self::NUMBERS, rand(0, strlen(self::NUMBERS)-1), 1);
    $password .= substr(self::SPECIAL_CHARACTERS, rand(0, strlen(self::SPECIAL_CHARACTERS)-1), 1);

    return $password;
  }

}




?>
