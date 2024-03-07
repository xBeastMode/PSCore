<?php
namespace PrestigeSociety\Auth\Task;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use PrestigeSociety\Auth\Handle\Sessions;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class LoginTimeoutTask extends Task{
        /** @var Player */
        private $player;
        /** @var PrestigeSocietyCore */
        private $core;

        /**
         * LoginTimeoutTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param Player              $player
         */
        public function __construct(PrestigeSocietyCore $core, Player $player){
                $this->player = $player;
                $this->core = $core;
        }

        /**
         * @param int $currentTick
         */
        public function onRun(int $currentTick){
                if($this->player->isOnline()){
                        if(Sessions::isUnAuthed($this->player)){
                                $this->player->kick(RandomUtils::colorMessage($this->core->getMessage("login", "timeout_kick_reason")));
                                Sessions::removeUnAuthed($this->player);
                        }
                }else{
                        Sessions::removeUnAuthed($this->player);
                }
        }
}