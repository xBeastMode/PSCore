<?php
namespace PrestigeSociety\Hats;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Pumpkin;
use pocketmine\block\Skull;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
class Hats{
        const ENABLED = 0;
        const DISABLED = 1;

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var HatEntity[] */
        protected array $hats = [];

        /**
         * Hats constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->core->getServer()->getPluginManager()->registerEvents(new HatsListener($core), $core);
        }

        public function despawnAll(){
                foreach($this->core->getServer()->getWorldManager()->getWorlds() as $level){
                        foreach($level->getEntities() as $entity){
                                if($entity instanceof HatEntity){
                                        $entity->close();
                                }
                        }
                }
        }

        /**
         * @param Item $item
         *
         * @return bool
         */
        public function isWearable(Item $item) : bool{
                if ($item instanceof Armor) {
                        return false;
                }
                $block = $item->getBlock();
                if ($block->getId() === BlockLegacyIds::AIR) {
                        return false;
                }
                return !($block instanceof Pumpkin) && !($block instanceof Skull);
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function hasHatEnabled(Player $player): bool{
                return isset($this->hats[spl_object_hash($player)]);
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function removeHat(Player $player): bool{
                $hat = $this->hats[spl_object_hash($player)] ?? null;
                if($hat !== null){
                        $hat->flagForDespawn();
                        unset($this->hats[spl_object_hash($player)]);

                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         * @param Item   $item
         */
        public function setHat(Player $player, Item $item){
                $this->removeHat($player);

                $block = $item->getBlock();
                $hat = new HatEntity($player->getLocation());

                $hat->setHatBlock($block->getId(), $block->getMeta());
                $hat->setInvisible(true);
                $hat->link($player);

                $hat->spawnToAll();

                $this->hats[spl_object_hash($player)] = $hat;
        }

        /**
         * @param Player $player
         */
        public function updateHat(Player $player){
                $hat = $this->hats[spl_object_hash($player)] ?? null;

                if($hat !== null){
                        $hat->updateHat($player);
                }
        }

        /**
         * @param Player $player
         * @param Item   $item
         *
         * @return int
         */
        public function toggleHat(Player $player, Item $item): int{
                if($this->hasHatEnabled($player)){
                        $this->removeHat($player);
                        return self::DISABLED;
                }else{
                        $this->setHat($player, $item);
                        return self::ENABLED;
                }
        }
}