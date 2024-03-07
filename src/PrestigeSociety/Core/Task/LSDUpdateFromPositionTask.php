<?php
namespace PrestigeSociety\Core\Task;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
class LSDUpdateFromPositionTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var Position|null */
        private ?Position $position = null;

        /** @var Block[] */
        protected array $processedBlocks = [];

        /** @var int */
        protected int $time = 0;

        /**
         * LSDUpdateFromPositionTask constructor.
         *
         * @param PrestigeSocietyCore $owner
         * @param Position            $position
         */
        public function __construct(PrestigeSocietyCore $owner, Position $position){
                $this->core = $owner;
                $this->position = $position;
        }

        public function onCancel(): void{
                foreach($this->processedBlocks as $index => $block){
                        $block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);
                        unset($this->processedBlocks[$index]);
                }
        }

        public function onRun(): void{
                $position = $this->position;

                /** @var Vector3[] $positions */
                $positions = [];

                for($x = -1; $x <= 1; $x++){
                        for($z = -1; $z <= 1; $z++){
                                $positions[] = $position->getWorld()->getBlock($position->subtract($x, 1, $z));
                        }
                }

                foreach($this->processedBlocks as $index => $block){
                        $block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);
                        unset($this->processedBlocks[$index]);
                }

                if($this->time % 10 === 0){
                        $this->getHandler()->cancel();
                }
                ++$this->time;

        }
}