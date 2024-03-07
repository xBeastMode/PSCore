<?php

namespace PrestigeSociety\Teleport\Task;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\Handle\TeleportQueue;
class TeleportDelayTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var Player */
        protected Player $player;
        /** @var Position */
        protected Position $position;
        /** @var int */
        protected int $delay;
        /** @var bool */
        protected bool $show_message;

        /**
         * TeleportDelayTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param Player              $player
         * @param Position            $position
         * @param int                 $delay
         * @param bool                $showMessage
         */
        public function __construct(PrestigeSocietyCore $core, Player $player, Position $position, int $delay, bool $showMessage = true){
                $this->core = $core;
                $this->player = $player;
                $this->position = $position;
                $this->delay = $delay;
                $this->show_message = $showMessage;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                if(!TeleportQueue::isInQueue($this->player)){
                        $this->getHandler()->cancel();
                        return;
                }

                if($this->show_message && $this->delay > 0){
                        $message = $this->core->module_loader->teleport->getMessage("teleport_queue");
                        $message = str_replace("@seconds", $this->delay, $message);

                        $this->player->sendActionBarMessage(RandomUtils::colorMessage($message));
                }

                if($this->delay-- <= 0){
                        $this->player->teleport($this->position);
                        TeleportQueue::removeFromQueue($this->player);

                        $this->getHandler()->cancel();
                        return;
                }
        }
}