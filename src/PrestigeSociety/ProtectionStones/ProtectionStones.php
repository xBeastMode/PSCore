<?php
namespace PrestigeSociety\ProtectionStones;
use JetBrains\PhpStorm\Pure;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\Stones\ConfirmBuyStoneForm;
use PrestigeSociety\Forms\FormList\Stones\ConfirmStoneDeletionForm;
use PrestigeSociety\Forms\FormList\Stones\ListHelpersForm;
use PrestigeSociety\Forms\FormList\Stones\ManageSingleStoneForm;
use PrestigeSociety\Forms\FormList\Stones\ManageStonesForm;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\Forms\FormList\Stones\RemoveHelperForm;
use PrestigeSociety\Forms\FormList\Stones\SelectStoneBoundsForm;
use PrestigeSociety\Forms\FormList\Stones\SetHelperForm;
use PrestigeSociety\Forms\FormList\Stones\TransferOwnershipForm;
use PrestigeSociety\Forms\FormManager;
class ProtectionStones{
        const STONES_DIR = "protection_stones/";

        /** @var Stone[] */
        protected array $stones = [];

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var array */
        public array $stones_config;

        /** @var int */
        public int $TRANSFER_OWNERSHIP = 0;
        public int $MANAGE_SINGLE_STONE_ID = 0;
        public int $MANAGE_STONES_ID = 0;
        public int $CONFIRM_DELETION_ID = 0;
        public int $SELECT_STONE_BOUNDS_ID = 0;
        public int $CONFIRM_BUY_STONE_ID = 0;
        public int $REMOVE_HELPER_ID = 0;
        public int $SET_HELPER_ID = 0;
        public int $LIST_HELPERS_ID = 0;
        public int $MESSAGE_ID = 0;

        /**
         * CombatLogger constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->parseStones();

                $this->stones_config = (new Config($core->getDataFolder() . "stones_config.yml", Config::YAML, ["cost_per_block" => 1000]))->getAll();
                $this->core->getServer()->getPluginManager()->registerEvents(new StonesListener($this->core), $this->core);

                $this->SELECT_STONE_BOUNDS_ID = FormManager::getNextFormId();
                $this->CONFIRM_DELETION_ID = FormManager::getNextFormId();
                $this->MANAGE_STONES_ID = FormManager::getNextFormId();
                $this->MANAGE_SINGLE_STONE_ID = FormManager::getNextFormId();
                $this->MESSAGE_ID = FormManager::getNextFormId();
                $this->LIST_HELPERS_ID = FormManager::getNextFormId();
                $this->SET_HELPER_ID = FormManager::getNextFormId();
                $this->REMOVE_HELPER_ID = FormManager::getNextFormId();

                $this->CONFIRM_BUY_STONE_ID = FormManager::getRandomUniqueId(); // random unique id because intervenes with existing form ids

                $this->core->module_loader->form_manager->registerHandler($this->SELECT_STONE_BOUNDS_ID, SelectStoneBoundsForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_BUY_STONE_ID, ConfirmBuyStoneForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_DELETION_ID, ConfirmStoneDeletionForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->MANAGE_STONES_ID, ManageStonesForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->MANAGE_SINGLE_STONE_ID, ManageSingleStoneForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->TRANSFER_OWNERSHIP, TransferOwnershipForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->MESSAGE_ID, MessageForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->LIST_HELPERS_ID, ListHelpersForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->SET_HELPER_ID, SetHelperForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->REMOVE_HELPER_ID, RemoveHelperForm::class);
        }

        /**
         * @return string
         */
        #[Pure] public function stonesDirectory(): string{
                return $this->core->getDataFolder() . self::STONES_DIR;
        }

        /**
         * @param Position $position
         *
         * @return null|Stone
         */
        #[Pure] public function getStoneAbsolute(Position $position): ?Stone{
                return $this->stones[$this->hashPosition($position)] ?? null;
        }

        /**
         * @param Position $position
         *
         * @return Stone[]
         */
        #[Pure] public function getStone(Position $position): array{
                $stones = [];
                foreach($this->stones as $stone){
                        if(RandomUtils::inBound($position, $stone->bounds) && ($position->getWorld()->getDisplayName() === $stone->position->getWorld()->getDisplayName())){
                                $stones[] = $stone;
                        }
                }
                return $stones;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function removeStone(Position $position): bool{
                if($this->getStoneAbsolute($position) !== null){
                        unset($this->stones[$this->hashPosition($position)]);
                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         *
         * @return Stone[]
         */
        #[Pure] public function getPlayerStones(Player $player): array{
                $stones = [];
                foreach($this->stones as $stone){
                        if($stone->isOwner($player->getName())){
                                $stones[] = $stone;
                        }
                }
                return $stones;
        }

        /**
         * @param Position $position
         *
         * @return string
         */
        #[Pure] protected function hashPosition(Position $position): string{
                return ($position->x . ":" . $position->y . ":" . $position->z . ":" . $position->getWorld()->getDisplayName());
        }

        public function parseStones(){
                $directory = $this->stonesDirectory();
                if(!file_exists($directory)){
                        mkdir($directory);
                }
                foreach(glob($directory . "*.json", GLOB_BRACE) as $stone){
                        $stoneData = json_decode(file_get_contents($stone), true);
                        $stone = Stone::parse($stoneData);

                        if($stone !== null){
                                $this->stones[$this->hashPosition($stone->position)] = $stone;
                        }
                }
        }

        /**
         * @param Stone $stone
         *
         * @return bool
         */
        #[Pure] public function stoneExists(Stone $stone): bool{
                return file_exists($this->stonesDirectory() . $stone->getOwner() . "." . $stone->getName() . ".json");
        }

        /**
         * @param Stone $stone
         */
        public function saveStone(Stone $stone){
                $this->stones[$this->hashPosition($stone->position)] = $stone;
                file_put_contents($this->stonesDirectory() . $stone->getOwner() . "." . $stone->getName() . ".json", json_encode($stone));
        }

        /**
         * @param Stone $stone
         *
         * @return bool
         */
        public function deleteStone(Stone $stone): bool{
                $directory = $this->stonesDirectory() . $stone->getOwner() . "." . $stone->getName() . ".json";
                if($this->getStoneAbsolute($stone->position) !== null && file_exists($directory)){
                        $this->removeStone($stone->position);
                        unlink($directory);
                        return true;
                }
                return false;
        }

        /**
         * @param AxisAlignedBB $bounds
         *
         * @return bool
         */
        #[Pure] public function boundsCollideWithStone(AxisAlignedBB $bounds): bool{
                foreach($this->stones as $stone){
                        if($stone->getBounds()->intersectsWith($bounds)){
                                return true;
                        }
                }
                return false;
        }

        /**
         * Checks if this stone collides with other stone not owned by player
         *
         * @param Player        $owner
         * @param AxisAlignedBB $bounds
         *
         * @return bool
         */
        #[Pure] public function boundsCollideWithUnaffiliatedStones(Player $owner, AxisAlignedBB $bounds): bool{
                foreach($this->stones as $stone){
                        if($stone->getBounds()->intersectsWith($bounds) && $stone->getOwner() !== $owner->getName()){
                                return true;
                        }
                }
                return false;
        }

        /**
         * @param Stone $stone
         *
         * @return bool
         */
        #[Pure] public function stoneCollidesWithStone(Stone $stone): bool{
                return $this->boundsCollideWithStone($stone->getBounds());
        }

        /**
         * Checks if this stone collides with other stone not owned by player
         *
         * @param Player $owner
         * @param Stone  $stone
         *
         * @return bool
         */
        #[Pure] public function stoneCollideWithUnaffiliatedStones(Player $owner, Stone $stone): bool{
                return $this->boundsCollideWithUnaffiliatedStones($owner, $stone->getBounds());
        }

        public function save(){
                foreach($this->stones as $stone){
                        file_put_contents($this->stonesDirectory() . $stone->getOwner() . "." . $stone->getName() . ".json", json_encode($stone));
                }
        }
}