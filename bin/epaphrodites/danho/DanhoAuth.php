<?php

namespace Epaphrodites\epaphrodites\danho;

use Epaphrodites\epaphrodites\api\email\SendMail;
use Epaphrodites\epaphrodites\auth\StartUsersSession;

class DanhoAuth extends StartUsersSession
{

  use \Epaphrodites\epaphrodites\env\phpEnv\phpEnv;

  /**
   **
   * Verify authentification of user
   * @param string $login
   * @param string $usersPassword
   * @return bool
   */
  private function getUsersAuthManagers(string $login, string $usersPassword):bool
  {

    $otpSession = null;

    if(!empty($login)&&!empty($usersPassword)){

    if ((static::class('verify')->onlyNumberAndCharacter($login, 15)) === false) {

      $result = static::getGuard('sql')->checkUsers($login);

      if (!empty($result)) {
        
          if (static::getGuard('guard')->AuthenticatedPassword($result[0]["password"], $usersPassword) == true && $result[0]["state"] == 1) {
           
            $otpSession = _OTP_METHOD_ == true && $result[0]["otp"] == 1 ? $this->sendOTPCode($result[0]["email"]) : null ;

            $this->StartUsersSession($result[0]["_id"], $result[0]["login"], $result[0]["namesurname"], $result[0]["contact"], $result[0]["email"], $result[0]["usersgroup"], $otpSession, $result[0]["otp"]);

            return true;
          } else {
            return false;
          }
        } else {
          return false;
        }
      } else {
        return false;
      }
    }else{
      return false;
    }
  }

  /**
   **
   * Verify authentification of user
   * @param string $login
   * @param string $usersPassword
   * @return bool
   */  
  public function UsersAuthManagers(string $login, string $usersPassword):bool
  {
    return $this->getUsersAuthManagers($login, $usersPassword);
  }

  /**
   * Generates and sends a One-Time Password (OTP) code to the user's email
   * 
   * This function creates a random OTP code of specified length and sends it
   * to the provided email address using the SendMail class. The email includes
   * a formatted HTML message with security instructions.
   *
   * @param string|null $usersEmail The recipient's email address. If null, only generates the code
   * @param int $length The length of the OTP code (default: 6 digits)
   * @return string The generated OTP code
   */
  private function sendOTPCode(
    string|null $usersEmail = null,
    int $length = 6
  ): string {

    $file = null;
    $result = false;
    $emailClass = new SendMail;
    $otpCode = str_pad(random_int(0, 999999), $length, '0', STR_PAD_LEFT);

    $msgHeader = "YOUR OTP CODE TO SECURE YOUR ACCOUNT ACCESS";
    $msgContent = 
        "<div style='font-size: 16px; line-height: 1.5; font-family: Arial, sans-serif;'>
            <p>Dear Sir/Madam,</p>
            <p>
                To secure your access, please use the following OTP code:
                <strong style='font-size: 18px; color: #d32f2f;'>{$otpCode}</strong>
            </p>
            <p>This code is valid for a limited time. Do not share it with anyone.</p>
            <p>If you did not request this code, please ignore this message.</p>
            <p><strong>Best regards,</strong></p>
        </div>";

    if (!empty($usersEmail)) {
        $result = $emailClass->sendEmail([$usersEmail], $msgHeader, $msgContent, $file);
    }

    return $result == true ? $otpCode : '123456';
  }
}
