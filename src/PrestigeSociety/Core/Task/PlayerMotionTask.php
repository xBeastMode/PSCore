<?php
namespace PrestigeSociety\Core\Task;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
class PlayerMotionTask extends Task{
        /** @var Player */
        protected Player $player;
        /** @var int */
        protected int $speedX;
        /** @var int */
        protected int $speedY;
        /** @var int */
        protected int $speedZ;

        /**
         * PlayerMotionTask constructor.
         * 
         * @param Player $player
         * @param int    $speedX
         * @param int    $speedY
         * @param int    $speedZ
         */
        public function __construct(Player $player, int $speedX, int $speedY, int $speedZ){
                $this->player = $player;
                $this->speedX = $speedX;
                $this->speedY = $speedY;
                $this->speedZ = $speedZ;
        }

        public function onRun(): void{
                if(!$this->player->isOnline()){
                        $this->getHandler()->cancel();
                        return;
                }

                $motion = $this->player->getDirectionVector();

                $motion->x *= $this->speedX;
                $motion->y *= $this->speedY;
                $motion->z *= $this->speedZ;

                $this->player->setMotion($motion);
        }
}