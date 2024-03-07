<?php
namespace PrestigeSociety\Core\Task;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
class HurtEntityByEntityTask extends Task{
        /** @var Entity|Player */
        protected Player|Entity $player;
        /** @var Entity */
        protected Entity $entity;
        /** @var int */
        protected int $seconds;
        /** @var int */
        protected int $hearts;
        /** @var int */
        protected int $time = 0;

        /** @var callable */
        protected $cancel;

        /**
         * HurtEntityByEntityTask constructor.
         * 
         * @param Entity $player
         * @param Entity $entity
         * @param int    $seconds
         * @param int    $hearts
         */
        public function __construct(Entity $player, Entity $entity, int $seconds, int $hearts){
                $this->player = $player;
                $this->entity = $entity;
                $this->seconds = $seconds;
                $this->hearts = $hearts;
        }

        public function onRun(): void{
                if(!$this->player->isOnline()){
                        $this->getHandler()->cancel();
                        return;
                }

                $attack = new EntityDamageByEntityEvent($this->entity, $this->player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->hearts, [], 0.4);
                $attack->setAttackCooldown(0);
                $this->player->attack($attack);

                if(++$this->time >= $this->seconds){
                        $this->getHandler()->cancel();
                }
        }
}