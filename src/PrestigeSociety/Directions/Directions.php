<?php
namespace PrestigeSociety\Directions;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\player\Player;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\Directions\DirectionsForm;
use PrestigeSociety\Forms\FormManager;
class Directions{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var Vector3[] */
        protected array $sessions = [];
        /** @var Item[] */
        protected array $compass = [];

        /** @var int */
        public int $DIRECTION_ID = 0;

        /**
         * Directions constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->DIRECTION_ID = FormManager::getNextFormId();
                $this->core->module_loader->form_manager->registerHandler($this->DIRECTION_ID, DirectionsForm::class);

                $this->core->getServer()->getPluginManager()->registerEvents(new DirectionsListener($core), $core);
        }

        /**
         * @param string   $name
         * @param Position $position
         */
        public function addDirection(string $name, Position $position){
                $this->core->module_configurations->directions[$position->getWorld()->getDisplayName()][$name] = [$position->x, $position->y, $position->z];
                $this->core->module_configurations->saveDirectionsConfig();
        }

        /**
         * @param string $world
         *
         * @return array
         */
        public function getDirectionsInWorld(string $world): array{
                return $this->core->module_configurations->directions[$world] ?? [];
        }

        /**
         * @param string $name
         */
        public function removeDirection(string $name){
                unset($this->core->module_configurations->directions[$name]);
                $this->core->module_configurations->saveDirectionsConfig();
        }

        /**
         * @param Player  $player
         * @param Vector3 $vector3
         */
        public function setSession(Player $player, Vector3 $vector3){
                $this->sessions[spl_object_hash($player)] = $vector3;
        }

        /**
         * @param Player $player
         *
         * @return null|Item
         */
        public function getCompass(Player $player): ?Item{
                return $this->compass[spl_object_hash($player)] ?? null;
        }

        /**
         * @param Player $player
         * @param Item   $item
         */
        public function setCompass(Player $player, Item $item){
                $this->compass[spl_object_hash($player)] = $item;
        }

        /**
         * @param Player $player
         */
        public function removeCompass(Player $player){
                $compass = $this->compass[spl_object_hash($player)] ?? null;
                if(!$player->isClosed() && $compass !== null){
                        $player->getInventory()->removeItem($compass);
                }
        }

        /**
         * @param Player $player
         *
         * @return null|Vector3
         */
        public function getSession(Player $player): ?Vector3{
                return $this->sessions[spl_object_hash($player)] ?? null;
        }

        /**
         * @param Player $player
         */
        public function removeSession(Player $player){
                unset($this->sessions[spl_object_hash($player)]);
        }

        /**
         * @param string $name
         * @param Player $player
         *
         * @return bool
         */
        public function giveDirections(string $name, Player $player): bool{
                $directions = $this->getDirectionsInWorld($player->getWorld()->getDisplayName());
                if(count($directions) > 0 && isset($directions[$name])){
                        $position = $directions[$name];

                        $vector = new Vector3(...$position);

                        $pk = new SetSpawnPositionPacket();
                        $pk->spawnPosition = BlockPosition::fromVector3($vector);
                        $pk->causingBlockPosition = BlockPosition::fromVector3($player->getWorld()->getSpawnLocation()->asVector3());
                        $pk->dimension = DimensionIds::OVERWORLD;
                        $pk->spawnType = SetSpawnPositionPacket::TYPE_WORLD_SPAWN;
                        $player->getNetworkSession()->sendDataPacket($pk);

                        $this->setSession($player, $vector);
                        $this->core->getScheduler()->scheduleRepeatingTask(new DestinationTask($this->core, $player, $vector), 1);

                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         */
        public function stopDirections(Player $player){
                $this->removeSession($player);
                $this->removeCompass($player);
        }
}