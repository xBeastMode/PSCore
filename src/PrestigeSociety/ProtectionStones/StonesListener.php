<?php
namespace PrestigeSociety\ProtectionStones;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use PrestigeSociety\Core\PrestigeSocietyCore;
class StonesListener implements Listener{
        const STONE_ID = 19;
        const STONE_META = 0;

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * StonesListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param PlayerInteractEvent $event
         *
         * @priority HIGHEST
         */
        public function onPlayerInteract(PlayerInteractEvent $event){
                $playerO = $event->getPlayer();
                $player = $event->getPlayer()->getName();
                $block = $event->getBlock();

                $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                if($tile !== null){
                        $stones = $this->core->module_loader->protection_stones->getStone($block->getPosition());
                        foreach($stones as $stone){
                                if($stone->isOwner($player) || $playerO->hasPermission("stones.interact")){
                                        continue;
                                }

                                if(!$stone->canInteract($player)){
                                        $event->cancel();
                                }
                        }
                }
        }

        /**
         * @param BlockBreakEvent $event
         *
         * @priority HIGHEST
         */
        public function onBlockBreak(BlockBreakEvent $event){
                $playerO = $event->getPlayer();
                $player = $event->getPlayer()->getName();
                $block = $event->getBlock();

                $stoneA = $this->core->module_loader->protection_stones->getStoneAbsolute($block->getPosition());
                if($stoneA !== null){
                        if($stoneA->isOwner($player) || $playerO->hasPermission("stones.delete")){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->CONFIRM_DELETION_ID, $event->getPlayer(), $stoneA);
                        }
                        $event->cancel();
                        return;
                }

                $stones = $this->core->module_loader->protection_stones->getStone($block->getPosition());
                foreach($stones as $stone){
                        if($stone->isOwner($player) || $playerO->hasPermission("stones.break")){
                                continue;
                        }

                        if(!$stone->canBreak($player)){
                                $event->cancel();
                        }
                }
        }

        /**
         * @param BlockPlaceEvent $event
         *
         * @priority HIGHEST
         */
        public function onBlockPlace(BlockPlaceEvent $event){
                $playerO = $event->getPlayer();
                $player = $event->getPlayer()->getName();
                $position = $event->getBlock();

                $stones = $this->core->module_loader->protection_stones->getStone($position->getPosition());

                if(count($stones) > 0){
                        foreach($stones as $stone){
                                if($stone->isOwner($player) || $playerO->hasPermission("stones.place")){
                                        continue;
                                }

                                if(!$stone->canPlace($player)){
                                        $event->cancel();
                                }elseif($position->getId() === self::STONE_ID && $position->getMeta() === self::STONE_META && $stone->isOwner($player) && !$event->isCancelled()){
                                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->SELECT_STONE_BOUNDS_ID, $event->getPlayer(), $position);
                                }
                        }
                }else{
                        if($position->getId() === self::STONE_ID && $position->getMeta() === self::STONE_META && !$event->isCancelled()){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->SELECT_STONE_BOUNDS_ID, $event->getPlayer(), $position);
                        }
                }
        }
}