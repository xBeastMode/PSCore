<?php
namespace PrestigeSociety\Teleport;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
class TeleportAPI{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * TeleportAPI constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /** @var Player[][] */
        public array $request_list = [];

        /**
         * @param Player $player
         */
        public function pushIntoRequests(Player $player){
                $this->request_list[$player->getName()] = [];
        }

        /**
         * @param Player $player
         */
        public function removeFromRequests(Player $player){
                if(isset($this->request_list[$player->getName()])){
                        unset($this->request_list[$player->getName()]);
                }
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        #[Pure] public function hasRequest(Player $player): bool{
                return isset($this->request_list[$player->getName()][1]);
        }

        /**
         * @param Player $to
         * @param Player $from
         *
         * @return bool
         */
        #[Pure] public function requestExists(Player $to, Player $from): bool{
                return isset($this->request_list[$to->getName()][1]) and ($this->request_list[$to->getName()][1] === $from);
        }

        /**
         * @param Player $to
         * @param Player $from
         */
        public function sendRequestTo(Player $to, Player $from){
                $this->request_list[$to->getName()] = [$to, $from];
        }

        /**
         * @param Player $to
         *
         * @return array
         */
        public function acceptRequest(Player $to): array{
                $to = $this->request_list[$to->getName()][0];
                $from = $this->request_list[$to->getName()][1];

                if($from->isOnline()){
                        unset($this->request_list[$to->getName()][1]);
                        return [true, $from];
                }

                unset($this->request_list[$to->getName()][1]);
                return [false, $from];
        }

        /**
         * @param Player $to
         *
         * @return array
         */
        public function denyRequest(Player $to): array{
                $to = $this->request_list[$to->getName()][0];
                $from = $this->request_list[$to->getName()][1];

                unset($this->request_list[$to->getName()][1]);
                if($from->isOnline()){
                        return [true, $from];
                }
                return [false, $from];
        }

        /**
         * @param Player $player
         *
         * @return int
         */
        public function getTeleportDelay(Player $player): int{
                return $this->core->module_loader->teleport->getTeleportDelay($player, ["module" => "teleport", "permission" => Teleport::INSTANT_TPA_TELEPORT_PERMISSION]);
        }
}