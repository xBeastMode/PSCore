<?php
namespace PrestigeSociety\Core\Task;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\Commands\HurtCommand;
class HurtPlayerTask extends Task{
        /** @var Player */
        protected Player $player;
        /** @var int */
        protected int $seconds;
        /** @var int */
        protected int $hearts;
        /** @var int */
        protected int $time = 0;

        /** @var callable */
        protected $cancel;

        /**
         * HurtPlayerTask constructor.
         * 
         * @param Player $player
         * @param int    $seconds
         * @param int    $hearts
         */
        public function __construct(Player $player, int $seconds, int $hearts){
                $this->player = $player;
                $this->seconds = $seconds;
                $this->hearts = $hearts;

                $this->cancel = function (){
                        $this->getHandler()->cancel();

                        if(isset(HurtCommand::$handlers[$this->player->getName()])){
                                unset(HurtCommand::$handlers[$this->player->getName()]);
                        }
                };
        }

        public function onRun(): void{
                if(!$this->player->isOnline()){
                        ($this->cancel)();
                        return;
                }

                $attack = new EntityDamageEvent($this->player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->hearts);
                $attack->setAttackCooldown(0);
                $this->player->attack($attack);

                if(++$this->time >= $this->seconds){
                        ($this->cancel)();
                }
        }
}