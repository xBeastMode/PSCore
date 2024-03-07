<?php
namespace PrestigeSociety\Auth;
use PrestigeSociety\Auth\Utils\AuthUtils;
use PrestigeSociety\Core\PrestigeSocietyCore;
class Auth extends AuthUtils{
        const WRONG_PASSWORD = 0;
        const PASSWORD_NOT_MATCH = 1;
        const CORRECT_PASSWORD = 2;
        const PASSWORD_CHANGE_SUCCESS = 3;
        const ERROR_NOT_REGISTERED = 4;
        const ERROR_UNKNOWN = 5;
        const REMOVE_REGISTER_SUCCESS = 6;

        /** @var PrestigeSocietyCore */
        protected $core;

        /**
         * Auth constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->core->getServer()->getPluginManager()->registerEvents(new AuthListener($this->core), $this->core);
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public function isRegistered($player){
                return false;
        }

        /**
         * @param $name
         *
         * @return bool
         */
        public function isRegisteredByName($name){
                return false;
        }

        /**
         * @param $player
         */
        public function addLoginSession($player){
        }

        /**
         * @param $player
         * @param $password
         *
         * @return int
         */
        public function tryAuth($player, $password): int{
                return self::ERROR_UNKNOWN;
        }

        /**
         * @param $player
         * @param $password
         */
        public function registerPlayer($player, $password){
        }

        /**
         * @param $player
         * @param $old
         *
         * @return int
         */
        public function unRegisterPlayer($player, $old): int{
                return self::ERROR_UNKNOWN;
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function unRegisterPlayerFromAdmin($player): int{
                return self::ERROR_UNKNOWN;
        }

        /**
         * @param $player
         * @param $old
         * @param $new
         *
         * @return bool
         */
        public function changePassword($player, $old, $new): bool{
                return true;
        }

        /**
         * @param $player
         * @param $new
         *
         * @return bool
         */
        public function changePasswordFromAdmin($player, $new){
                return true;
        }

        /**
         * @param $player
         *
         * @return null|string
         */
        private function getPlayerPassword($player): ?string{
                return null;
        }

        /**
         * @param $player
         *
         * @return array|null
         */
        private function getPlayerData($player): ?array{
                return null;
        }
}