<?php
namespace PrestigeSociety\Core;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\FlameParticle;
use PrestigeSociety\Core\Task\LSDUpdateTask;
use PrestigeSociety\Core\Task\ParticleCircleTask;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\SoundNames;
class FunBox{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var array */
        public array $lsd_enabled = [];
        /** @var array */
        public array $god_mode_enabled = [];
        /** @var int[] */
        public array $generated_colors = [];
        /** @var Player[] */
        protected array $frozen = [];
        /** @var Player[] */
        protected array $hidden = [];
        /** @var string[] */
        protected array $named_tags = [];

        /** @var ParticleCircleTask[] */
        protected array $god_particle_task = [];

        /** @var Player[][] */
        protected array $ignore_list = [];

        /**
         * FunBox constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @return int[]
         */
        public function generateColors(): array{
                $this->generated_colors = [];
                $colors = 1000;
                $k = mt_rand(0xFFF, 0xFFFFFF);
                $f = 0;
                $div = $colors / 2;
                for($i = 0; $i <= $colors; ++$i){
                        if($i > $div){
                                $k = $k - $f;
                                $f--;
                        }else{
                                $k = $k + $f;
                                $f++;
                        }
                        $this->generated_colors[$i] = $k;
                }
                return $this->generated_colors;
        }

        /**
         * @param Player $sender
         *
         * @return bool
         */
        #[Pure] public function isLSDEnabled(Player $sender): bool{
                return isset($this->lsd_enabled[$sender->getXuid()]);
        }

        /**
         * @param Player $sender
         *
         * @return bool
         */
        public function enableLSD(Player $sender): bool{
                if($this->isLSDEnabled($sender)) return false;
                $xuid = $sender->getXuid();

                $lsdUT = new LSDUpdateTask($this->core, $sender);
                $id = $this->core->getScheduler()->scheduleRepeatingTask($lsdUT, 1);

                $sender->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 0x7fffffff, 50));

                $this->lsd_enabled[$xuid]["player"] = $sender;
                $this->lsd_enabled[$xuid]["task_id"] = $id;
                $this->lsd_enabled[$xuid]["inventory"] = $sender->getInventory()->getContents();
                $this->lsd_enabled[$xuid]["armor_inventory"] = $sender->getArmorInventory()->getContents(true);

                $sender->getInventory()->clearAll();

                $message = $this->core->getMessage("LSD", "enabled");

                RandomUtils::playSound(SoundNames::SOUND_PORTAL_PORTAL, $sender->getPosition(), single: true);
                $sender->sendMessage(RandomUtils::colorMessage($message));
                return true;
        }

        /**
         * @param Player $sender
         *
         * @return bool
         */
        public function disableLSD(Player $sender): bool{
                if(!$this->isLSDEnabled($sender)) return false;
                $xuid = $sender->getXuid();

                /** @var TaskHandler $id */
                $id = $this->lsd_enabled[$xuid]["task_id"];
                $id->cancel();

                $sender->getInventory()->setContents($this->lsd_enabled[$xuid]["inventory"]);
                $sender->getArmorInventory()->setContents($this->lsd_enabled[$xuid]["armor_inventory"]);

                unset($this->lsd_enabled[$xuid]);

                $message = $this->core->getMessage("LSD", "disabled");
                $sender->sendMessage(RandomUtils::colorMessage($message));
                return true;
        }

        /**
         * @param Player $sender
         *
         * @return bool
         */
        public function toggleLSD(Player $sender): bool{
                $event = $this->core->module_loader->events->onToggleLSD($sender, !$this->isLSDEnabled($sender));

                if(!$event->isCancelled()){
                        if(!$event->isEnabled()){
                                $this->disableLSD($sender);
                        }else{
                                $this->enableLSD($sender);
                        }
                        return true;
                }
                return false;
        }

        /**
         * @param Player $sender
         *
         * @return bool
         */
        #[Pure] public function isGodEnabled(Player $sender): bool{
                return isset($this->god_mode_enabled[$sender->getXuid()]);
        }

        /**
         * @param Player $sender
         *
         * @return bool
         */
        public function enableGod(Player $sender): bool{
                if($this->isGodEnabled($sender)) return false;
                $xuid = $sender->getXuid();

                $task = new ParticleCircleTask($this->core, $sender, new FlameParticle(), -1);
                $this->core->getScheduler()->scheduleRepeatingTask($task, 0);

                $this->god_particle_task[$xuid] = $task;
                $this->god_mode_enabled[$xuid] = $sender;

                $message = $this->core->getMessage("God", "enabled");
                $sender->sendMessage(RandomUtils::colorMessage($message));

                RandomUtils::playSound(SoundNames::SOUND_BLOCK_END_PORTAL_SPAWN, $sender->getPosition(), single: true);
                return true;
        }

        /**
         * @param Player $sender
         *
         * @return bool
         */
        public function disableGod(Player $sender): bool{
                if(!$this->isGodEnabled($sender)) return false;
                $xuid = $sender->getXuid();

                $this->god_particle_task[$xuid]->getHandler()->cancel();

                unset($this->god_particle_task[$xuid]);
                unset($this->god_mode_enabled[$xuid]);

                $message = $this->core->getMessage("God", "disabled");
                $sender->sendMessage(RandomUtils::colorMessage($message));
                return true;
        }

        /**
         * @param Player $sender
         *
         * @return bool
         */
        public function toggleGod(Player $sender): bool{
                $event = $this->core->module_loader->events->onToggleGod($sender, !$this->isGodEnabled($sender));

                if(!$event->isCancelled()){
                        if(!$event->isEnabled()){
                                $this->disableGod($sender);
                        }else{
                                $this->enableGod($sender);
                        }
                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function toggleFlight(Player $player): bool{
                $event = $this->core->module_loader->events->onToggleFlight($player, !$player->getAllowFlight());

                if(!$event->isCancelled()){
                        $player->setAllowFlight($event->isEnabled());

                        $player->sendTip(RandomUtils::colorMessage(($player->getAllowFlight() ? "&l&8» &eFLIGHT IS NOW &aON" : "&l&8» &eFLIGHT IS NOW &cOFF")));
                        RandomUtils::playSound(SoundNames::SOUND_BLOCK_END_PORTAL_FRAME_FILL, $player->getPosition(), single: true);

                        if(!$player->getAllowFlight()){
                                $player->setFlying(false);
                        }
                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function enableFlight(Player $player): bool{
                if(!$player->getAllowFlight()){
                        $this->toggleFlight($player);
                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function disableFlight(Player $player): bool{
                if($player->getAllowFlight()){
                        $this->toggleFlight($player);
                        return true;
                }
                return false;
        }

        /**
         * @param Player $sender
         * @param Player $player
         * @param bool   $cancelCommands
         *
         * @return bool
         */
        public function toggleFreeze(Player $sender, Player $player, bool $cancelCommands): bool{
                $event = $this->core->module_loader->events->onToggleFreeze($sender, $player, !isset($this->frozen[$player->getName()]), $cancelCommands);

                if(!$event->isCancelled()){
                        if(!$event->isEnabled()){
                                $player->setImmobile(false);
                                unset($this->frozen[$player->getName()]);

                                $player->sendMessage(TextFormat::GREEN . "You are no longer frozen.");
                                $sender->sendMessage(TextFormat::GREEN . "{$player->getName()} is no longer frozen.");
                        }else{
                                $player->setImmobile(true);
                                $this->frozen[$player->getName()] = $player;

                                $player->sendMessage(TextFormat::GREEN . "You are now frozen.");
                                $sender->sendMessage(TextFormat::GREEN . "Successfully froze {$player->getName()}" . ($event->cancelCommands() ? ", they can no longer run commands." : "."));
                        }
                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function toggleHide(Player $player): bool{
                $event = $this->core->module_loader->events->onToggleHide($player, !isset($this->hidden[$player->getName()]));

                if(!$event->isCancelled()){
                        if(!$event->isEnabled()){
                                $player->setInvisible(false);
                                $player->getServer()->addOnlinePlayer($player);
                                unset($this->hidden[$player->getName()]);

                                $player->sendMessage(TextFormat::GREEN . "You are no longer hidden");
                        }else{
                                $player->setInvisible(true);
                                $player->getServer()->removeOnlinePlayer($player);

                                $this->hidden[$player->getName()] = $player;

                                $player->sendMessage(TextFormat::GREEN . "You are now hidden from everyone else.");
                        }
                        return true;
                }
                return false;
        }

        /**
         * @param CommandSender $sender
         * @param Player        $player
         *
         * @return bool
         */
        public function toggleHideName(CommandSender $sender, Player $player): bool{
                $event = $this->core->module_loader->events->onToggleHideName($player, !$player->isNameTagVisible());

                if(!$event->isCancelled()){
                        if($event->isEnabled()){
                                $player->setNameTagVisible(true);
                                $player->setNameTag($this->core->module_loader->chat->formatDisplayName($player));
                                $player->setDisplayName($this->named_tags[$player->getName()]);

                                unset($this->named_tags[$player->getName()]);

                                $player->sendMessage(TextFormat::GREEN . "Your name is no longer hidden.");
                                $sender->sendMessage(TextFormat::GREEN . "{$player->getName()}'s name is no longer hidden.");
                        }else{
                                $this->named_tags[$player->getName()] = $player->getDisplayName();

                                $player->setNameTagVisible(false);
                                $player->setNameTag("");
                                $player->setDisplayName("");

                                $player->sendMessage(TextFormat::GREEN . "Your name is now hidden.");
                                $sender->sendMessage(TextFormat::GREEN . "{$player->getName()}'s name is now hidden.");
                        }
                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         * @param Player $target
         *
         * @return bool
         */
        #[Pure] public function isIgnoring(Player $player, Player $target): bool{
                return isset($this->ignore_list[$player->getName()][$target->getName()]);
        }

        /**
         * @param Player $player
         * @param string $target
         *
         * @return bool
         */
        #[Pure] public function isIgnoringUser(Player $player, string $target): bool{
                return isset($this->ignore_list[$player->getName()][$target]);
        }

        /**
         * @param Player $player
         * @param Player $target
         *
         * @return bool
         */
        public function toggleIgnore(Player $player, Player $target): bool{
                if(!isset($this->ignore_list[$player->getName()])){
                        $this->ignore_list[$player->getName()] = [];
                }

                $event = $this->core->module_loader->events->onToggleIgnore($player, $target, !$this->isIgnoring($player, $target));

                if(!$event->isCancelled()){
                        if(!$event->isEnabled()){
                                unset($this->ignore_list[$player->getName()][$target->getName()]);

                                $message = $this->core->getMessage("ignore", "ignore_stop");
                                $message = str_replace("@player", $target->getName(), $message);
                                $player->sendMessage(RandomUtils::colorMessage($message));
                        }else{
                                $this->ignore_list[$player->getName()][$target->getName()] = $player;

                                $message = $this->core->getMessage("ignore", "ignore_start");
                                $message = str_replace("@player", $target->getName(), $message);
                                $player->sendMessage(RandomUtils::colorMessage($message));
                        }
                        return false;
                }
                return true;
        }
}