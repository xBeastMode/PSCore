<?php

namespace PrestigeSociety\Core\Task;
use pocketmine\block\Block;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\world\particle\DustParticle;
use PrestigeSociety\Core\PrestigeSocietyCore;
class LSDUpdateTask extends Task{

        /** @var Player|null */
        private ?Player $player = null;

        /** @var int */
        protected int $next = 0;
        /** @var int */
        protected int $len = 0;
        /** @var array */
        protected array $colors = [];

        /** @var Block[] */
        protected array $processedBlocks = [];

        /** @var bool */
        protected bool $increasing = true;

        /**
         * LSDUpdateTask constructor.
         *
         * @param PrestigeSocietyCore $owner
         * @param Player              $player
         */
        public function __construct(PrestigeSocietyCore $owner, Player &$player){
                $this->player = $player;
                $this->colors = $owner->module_loader->fun_box->generateColors();
                $this->len = count($this->colors) - 1;
        }

        public function onCancel(): void{
                foreach($this->processedBlocks as $index => $block){
                        $block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);
                        unset($this->processedBlocks[$index]);
                }
        }

        public function onRun(): void{
                if($this->next >= $this->len){
                        $this->next = 0;
                }

                $player = $this->player;

                /** @var Vector3[] $positions */
                $positions = [];

                if($player->isSneaking()){
                        $positions[] = $player->getWorld()->getBlock($player->getLocation()->subtract(0, 2, 0));
                }else{
                        $positions[] = $player->getWorld()->getBlock($player->getLocation()->subtract(0, 1, 0));
                }

                $player->getWorld()->addParticle($player->getLocation(), new DustParticle(new Color(mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255))));

                foreach($this->processedBlocks as $index => $block){
                        $block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);
                        unset($this->processedBlocks[$index]);
                }

                $block = VanillaBlocks::WOOL();
                $dyeColor = DyeColor::getAll();

                foreach($positions as $position){
                        $vector = $position->getPosition()->asVector3();

                        $block->setColor($dyeColor[array_rand($dyeColor)]);
                        $this->processedBlocks[] = $player->getWorld()->getBlock($vector);
                        $player->getWorld()->setBlock($vector, $block);
                }

                $helmet = VanillaItems::LEATHER_CAP();
                $chest = VanillaItems::LEATHER_TUNIC();
                $legs = VanillaItems::LEATHER_PANTS();
                $feet = VanillaItems::LEATHER_BOOTS();

                $nbt = CompoundTag::create();

                $nbt->setInt("customColor", $this->colors[$this->next++]);

                $helmet->setNamedTag($nbt);
                $chest->setNamedTag($nbt);
                $legs->setNamedTag($nbt);
                $feet->setNamedTag($nbt);

                $this->player->getArmorInventory()->setContents([$helmet, $chest, $legs, $feet]);

        }
}