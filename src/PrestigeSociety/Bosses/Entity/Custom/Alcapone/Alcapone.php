<?php

namespace PrestigeSociety\Bosses\Entity\Custom\Alcapone;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\ListTag;
use PrestigeSociety\Bosses\Entity\BossEntity;
use PrestigeSociety\Core\Entities\ItemProjectile;
use PrestigeSociety\Core\Utils\RandomUtils;
class Alcapone extends BossEntity{
        /** @var Hitman[] */
        protected array $children = [];

        public function onDespawn(){
                foreach($this->children as $child) $child->close();
        }

        /**
         * @param Entity $entity
         * @param Item   $item
         * @param int    $force
         *
         * @return ItemProjectile
         * @throws \JsonException
         */
        public function makeProjectile(Entity $entity, Item $item, int $force = 2): ItemProjectile{
                $skinData = str_repeat("\x00", 64 * 64 * 2);

                $dir = $entity->getDirectionVector();
                $dir->x *= $force;
                $dir->z *= $force;

                $nbt = RandomUtils::generateSkinCompoundTag($skinData);
                $nbt->setTag("HandItems", new ListTag([$item->nbtSerialize()]));

                $projectile = new ItemProjectile($entity->getLocation(), new Skin("Steve" . time(), $skinData, ), $nbt);

                $projectile->itemProjectile = $item;
                $projectile->shootingEntity = $entity;

                $projectile->setInvisible(true);

                return $projectile;
        }

        /**
         * @throws \JsonException
         */
        public function entityBaseTick(int $tickDiff = 1): bool{
                if($this->goal !== null && !$this->goal->isClosed() && $this->goal->isAlive()){
                        if($this->ability_used && ($this->ticksLived % 2 === 0)){
                                $this->makeProjectile($this, VanillaItems::IRON_INGOT())->spawnToAll();
                                RandomUtils::playSound("firework.blast", $this, 1000, 0.4);
                        }

                        if((mt_rand(1, 100) <= 5) && (count($this->children) < 3)){
                                $hitman = new Hitman($this->getLocation(), $this->skin);

                                $hitman->setNameTag("Alcapone's Hitman");
                                $hitman->setNameTagAlwaysVisible(true);
                                $hitman->setSkin($this->skin);

                                $hitman->getInventory()->setHeldItemIndex(0);
                                $hitman->getInventory()->setItem(0, $this->getInventory()->getItemInHand());

                                $hitman->setPosition($this->getPosition());

                                $hitman->parent = $this;
                                $this->children [] = $hitman;

                                $this->children = array_values($this->children);
                                $hitman->spawnToAll();
                        }

                        foreach($this->children as $i => $child){
                                if($child->isClosed() || !$child->isAlive()){
                                        unset($this->children[$i]);
                                }

                                $child->goal = $this->getPosition();
                                $child->attacking = $this->attacking;
                                $child->setTargetEntity($this->goal);

                                switch($i){
                                        case 0:
                                                $child->goal->x += mt_rand(1, 2);
                                                $child->goal->z += mt_rand(1, 2);
                                                break;
                                        case 1:
                                                $child->goal->x += -mt_rand(1, 2);
                                                $child->goal->z += -mt_rand(1, 2);
                                                break;
                                        case 2:
                                                $child->goal->x += -mt_rand(1, 2);
                                                $child->goal->z += mt_rand(1, 2);
                                                break;
                                }
                        }
                }

                return parent::entityBaseTick($tickDiff);
        }
}