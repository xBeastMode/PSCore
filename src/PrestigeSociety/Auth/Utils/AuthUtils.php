<?php
namespace PrestigeSociety\Auth\Utils;
class AuthUtils{
        /**
         * @param $length
         * @param $salt
         *
         * @return bool|string
         */
        public function randomPasswordHashed($length, $salt){
                return $this->hashPassword($this->randomPassword($length), $salt);
        }

        /**
         * @param int $length
         * 
         * @return string
         */
        public function randomPassword(int $length){
                $abc = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
                $password = "";
                for($i = 0; $i < $length + 1; $i++){
                        $password .= $abc[rand(0, strlen($abc) - 1)];
                }
                return $password;
        }

        /**
         * @param $password
         * @param $salt
         * 
         * @return bool|string
         */
        protected function hashPassword($password, $salt){
                return password_hash($password . $salt, PASSWORD_BCRYPT, ["cost" => 10]);
        }

        /**
         * @param $password
         * @param $hashed
         * 
         * @return bool
         */
        protected function verifyPassword($password, $hashed){
                return password_verify($password, $hashed);
        }
}