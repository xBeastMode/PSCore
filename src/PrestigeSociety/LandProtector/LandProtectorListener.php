<?php
namespace PrestigeSociety\LandProtector;
use pocketmine\block\Block;
use pocketmine\block\Fire;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use PrestigeSociety\Bosses\Entity\BossEntity;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\LandProtector\Handle\Sessions;
class LandProtectorListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * LandProtectorListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param Player $player
         * @param Block  $block
         *
         * @return bool
         */
        protected function checkSelection(Player $player, Block $block): bool{
                $name = $player->getName();

                if(isset(Sessions::$selections1[$name]) and $player->hasPermission("land.protector.selection.1")){
                        Sessions::$setter1[$name] = $block->getPosition();

                        $message = $this->core->getMessage("land_protector", "successful_selection_1");
                        $message = str_replace(["@x", "@y", "@z"], [$block->getPosition()->x, $block->getPosition()->y, $block->getPosition()->z], $message);

                        $player->sendMessage(RandomUtils::colorMessage($message));

                        unset(Sessions::$selections1[$name]);
                        return true;
                }

                if(isset(Sessions::$selections2[$name]) and $player->hasPermission("land.protector.selection.2")){
                        Sessions::$setter2[$name] = $block->getPosition();

                        $message = $this->core->getMessage("land_protector", "successful_selection_2");
                        $message = str_replace(["@x", "@y", "@z"], [$block->getPosition()->x, $block->getPosition()->y, $block->getPosition()->z], $message);

                        $player->sendMessage(RandomUtils::colorMessage($message));

                        unset(Sessions::$selections2[$name]);
                        return true;
                }

                return false;

        }

        /**
         * @param BlockBreakEvent $event
         *
         * @PRIORITY LOWEST
         */
        public function onBlockBreak(BlockBreakEvent $event){
                $player = $event->getPlayer();
                $block = $event->getBlock();
                if($this->checkSelection($player, $block)){
                        $event->cancel();
                        return;
                }
                $mod = $this->core->module_loader->land_protector;
                if($mod->isInMine($block->getPosition())){
                        return;
                }
                $names = $mod->getAreasByVector($player->getLocation(), $player->getWorld());

                if(!empty($names)){
                        $canChange = true;
                        foreach($names as $name2) if($mod->isWhitelisted($name2, $player)) $canChange = false;
                        if(!$canChange) return;
                }

                if($mod->canForceEdit($block->getPosition())){
                        return;
                }

                if(!$mod->canEditWold($player->getWorld()->getDisplayName()) and !$player->hasPermission("land.protector.edit.break.bypass")){
                        $event->cancel();
                }
                if(!$mod->canEdit($block->getPosition()) and !$player->hasPermission("land.protector.edit.break.bypass")){
                        $event->cancel();
                }
        }

        /**
         * @param BlockPlaceEvent $event
         *
         * @PRIORITY LOWEST
         */
        public function onBlockPlace(BlockPlaceEvent $event){
                $player = $event->getPlayer();
                $block = $event->getBlockReplaced();
                $level = $block->getPosition()->getWorld();

                if($this->checkSelection($player, $block)){
                        $event->cancel();
                        return;
                }

                $mod = $this->core->module_loader->land_protector;
                $names = $mod->getAreasByVector($block->getPosition(), $level);

                if(!empty($names)){
                        $canChange = true;
                        foreach($names as $name2) if($mod->isWhitelisted($name2, $player)) $canChange = false;
                        if(!$canChange) return;
                }

                if($mod->canForceEdit($block->getPosition())){
                        return;
                }

                if(!$mod->canEditWold($level->getDisplayName()) and !$player->hasPermission("land.protector.edit.place.bypass")){
                        $event->cancel();
                }
                if(!$mod->canEdit($block->getPosition()) and !$player->hasPermission("land.protector.edit.place.bypass")){
                        $event->cancel();
                }
        }

        /**
         * @param EntityDamageEvent $event
         *
         * @PRIORITY LOWEST
         */
        public function onEntityDamage(EntityDamageEvent $event){
                $entity = $event->getEntity();
                $mod = $this->core->module_loader->land_protector;

                if($event instanceof EntityDamageByEntityEvent && $event->getDamager() instanceof BossEntity){
                        return;
                }

                if($mod->canForceDamage($entity->getPosition())){
                        return;
                }

                if($entity instanceof Player and !$mod->canDamageWold($entity->getWorld()->getDisplayName())){
                        $event->cancel();
                }
                if($entity instanceof Player and !$mod->canDamage($entity->getPosition())){
                        $event->cancel();
                }
        }

        /**
         * @param PlayerInteractEvent $event
         *
         * @PRIORITY LOWEST
         */
        public function onPlayerInteract(PlayerInteractEvent $event){
                $block = $event->getBlock();

                $player = $event->getPlayer();
                $mod = $this->core->module_loader->land_protector;
                $names = $mod->getAreasByVector($player->getLocation(), $player->getWorld());

                if(!empty($names)){
                        $white_listed = false;
                        foreach($names as $name) if($mod->isWhitelisted($name, $player)) $white_listed = true;
                        if($white_listed) return;
                }

                if($mod->canForceTouch($block->getPosition())){
                        return;
                }

                if(!$mod->canTouchWold($player->getWorld()->getDisplayName()) and !$player->hasPermission("land.protector.touch.bypass")){
                        $event->cancel();
                }
                if(!$mod->canTouch($block->getPosition()) and !$player->hasPermission("land.protector.touch.bypass")){
                        $event->cancel();
                }
        }

        /**
         * @param BlockBurnEvent $event
         *
         * @priority LOWEST
         */
        public function onBlockBurn(BlockBurnEvent $event){
                $position = $event->getBlock()->getPosition();
                $mod = $this->core->module_loader->land_protector;

                if($mod->canForceBurn($position)){
                        return;
                }

                if(!$mod->canBurnWorld($position->getWorld()->getDisplayName())){
                        $event->cancel();
                }
                if(!$mod->canBurn($position)){
                        $event->cancel();
                }
        }

        /**
         * @param BlockUpdateEvent $event
         *
         * @priority LOWEST
         */
        public function onBlockSpread(BlockUpdateEvent $event){
                $block = $event->getBlock();

                if($block instanceof Fire){
                        $position = $event->getBlock()->getPosition();
                        $mod = $this->core->module_loader->land_protector;

                        if($mod->canForceBurn($position)){
                                return;
                        }

                        if(!$mod->canBurnWorld($position->getWorld()->getDisplayName())){
                                $event->cancel();
                        }
                        if(!$mod->canBurn($position)){
                                $event->cancel();
                        }
                }
        }

        /**
         * @param EntityExplodeEvent $event
         *
         * @priority LOWEST
         */
        public function onEntityExplode(EntityExplodeEvent $event){
                $position = $event->getPosition();
                $mod = $this->core->module_loader->land_protector;

                if($mod->canForceExplode($position)){
                        return;
                }

                if(!$mod->canExplodeWorld($position->getWorld()->getDisplayName())){
                        $event->setBlockList([]);
                }
                if(!$mod->canExplode($position)){
                        $event->setBlockList([]);
                }
        }
}