<?php
namespace PrestigeSociety\Core;
use aieuo\mineflow\flowItem\action\world\DropItem;
use brokiem\snpc\entity\BaseNPC;
use brokiem\snpc\entity\CustomHuman;
use brokiem\snpc\manager\NPCManager;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\Block;
use pocketmine\block\ItemFrame;
use pocketmine\block\tile\EnderChest;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\object\Painting;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\nbt\BaseNbtSerializer;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\SellCommand;
use PrestigeSociety\Core\Task\HurtPlayerForceTask;
use PrestigeSociety\Core\Task\WelcomePlayerTask;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\SoundNames;
use PrestigeSociety\Management\StaticManagement;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\PowerUps\PowerUps;
class EventListener implements Listener{

        /** @var PrestigeSocietyCore */
        private PrestigeSocietyCore $core;

        protected array $fall_protection = [];
        /** @var Block[] */
        protected array $last_safe_block = [];

        protected array $auto_sell_worlds = [];
        protected array $command_cooldown = ["global" => []];
        protected array $command_cooldown_config = [];

        /** @var int[][] */
        protected array $sell_log = [];
        /** @var int[] */
        protected array $sell_log_timer = [];

        /** @var int|array */
        public static int|array $damage_times = [];

        /**
         * EventListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->auto_sell_worlds = $core->getConfig()->get("auto_sell_worlds");
                $this->command_cooldown_config = $core->getConfig()->get("command_cooldown");
        }

        /**
         * @priority LOWEST
         *
         * @param QueryRegenerateEvent $event
         */
        public function onQueryRegenerate(QueryRegenerateEvent $event){
                $event->getQueryInfo()->setPlugins([$this->core]);
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerRespawnEvent $event
         */
        public function onPlayerRespawn(PlayerRespawnEvent $event){
                $event->setRespawnPosition($this->core->module_loader->teleport->getSpawn());
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerExhaustEvent $event
         */
        public function onPlayerExhaust(PlayerExhaustEvent $event){
                if($event->getPlayer()->getWorld()->getDisplayName() === $this->core->module_loader->teleport->getSpawn()->getWorld()->getDisplayName()){
                        $event->cancel();
                }
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerToggleSneakEvent $event
         */
        public function onPlayerToggleSneak(PlayerToggleSneakEvent $event){
                $this->core->module_loader->entity_linker->tryUnlink($event->getPlayer());
        }

        /**
         * @priority LOWEST
         *
         * @param EntityTeleportEvent $event
         */
        public function onEntityTeleport(EntityTeleportEvent $event){
                $entity = $event->getEntity();
                $this->core->module_loader->entity_linker->tryUnlink($entity);

                if($entity instanceof Player){
                        $this->fall_protection[spl_object_hash($entity)] = true;
                }
        }

        /**
         * @priority LOWEST
         *
         * @param EntityDespawnEvent $event
         */
        public function onEntityDespawn(EntityDespawnEvent $event){
                $this->core->module_loader->entity_linker->tryUnlink($event->getEntity());
        }

        /**
         * @priority LOWEST
         *
         * @param EntitySpawnEvent $event
         */
        public function onEntitySpawn(EntitySpawnEvent $event){
                $entity = $event->getEntity();
                $class = get_class($entity);

                if(in_array($class, $this->core->getConfig()->get("disabled_entities"), true)){
                        $entity->flagForDespawn();
                }
        }

        /**
         * @priority LOWEST
         *
         * @param DataPacketReceiveEvent $event
         */
        public function onDataPacketReceive(DataPacketReceiveEvent $event){
                $packet = $event->getPacket();


                if($packet instanceof ItemFrameDropItemPacket && !$event->getOrigin()->getPlayer()->hasPermission("command.framedrop")){
                        $event->cancel();
                }

                if($packet instanceof InteractPacket && $packet->action === InteractPacket::ACTION_LEAVE_VEHICLE){
                        $this->core->module_loader->entity_linker->tryUnlink($event->getOrigin()->getPlayer());
                }
        }

        public function onPlayerDropItem(PlayerDropItemEvent $event){
                $player = $event->getPlayer();
                $item = $event->getItem();

                if($this->core->getConfig()->get("lock_cash") && !$player->hasPermission("cashlock.bypass")){
                        if($this->core->module_loader->economy->isCashItem($item)){
                                RandomUtils::playSound(SoundNames::SOUND_NOTE_BASS, $player, 500, 1, true);
                                $event->cancel();
                        }
                }
        }


        /**
         * @param InventoryTransactionEvent $event
         */
        public function onInventoryTransaction(InventoryTransactionEvent $event){
                $transaction = $event->getTransaction();
                $player = $transaction->getSource();

                if($this->core->getConfig()->get("lock_cash") && !$player->hasPermission("cashlock.bypass")){
                        foreach($transaction->getActions() as $action){
                                if($action instanceof SlotChangeAction){
                                        if($action->getInventory() !== $player->getInventory() && $this->core->module_loader->economy->isCashItem($action->getTargetItem())){
                                                RandomUtils::playSound(SoundNames::SOUND_NOTE_BASS, $player, 500, 1, true);
                                                $event->cancel();
                                        }
                                }

                        }
                }
        }

        /**
         * @param PlayerMoveEvent $event
         */
        public function onPlayerMove(PlayerMoveEvent $event){
                $to = $event->getTo();
                $from = $event->getFrom();
                $player = $event->getPlayer();

                $under_block = $player->getWorld()->getBlock($to->subtract(0, 1, 0));
                if($under_block->isSolid()){
                        $this->last_safe_block[spl_object_hash($player)] = $under_block;
                }
                $under_block = $this->last_safe_block[spl_object_hash($player)] ?? $under_block;

                $distance = $to->distance($from);
                if($distance > 0.1 && $to->y <= 0){
                        $player->teleport($under_block->getPosition()->floor()->add(0, 1, 0));
                        RandomUtils::playSound("mob.enderdragon.hit", $player, 500, 1, true);

                        $this->core->getScheduler()->scheduleRepeatingTask(new HurtPlayerForceTask($player, 4, 1), 0);
                }

                $max_plain_distance = $to->getWorld()->getSpawnLocation()->maxPlainDistance($player->getLocation());

                $border_worlds = $this->core->module_configurations->borders;
                $world_name = $player->getWorld()->getDisplayName();

                if(isset($border_worlds[$world_name]) && $max_plain_distance > $border_worlds[$world_name]){
                        $player->sendTip(RandomUtils::colorMessage($this->core->getMessage("borders", "reached")));

                        if(!$player->hasPermission("border.bypass")) $event->cancel();

                        if($max_plain_distance > ++$border_worlds[$world_name]){
                                if(!$player->hasPermission("border.bypass")){
                                        $player->sendTip(RandomUtils::colorMessage($this->core->getMessage("borders", "cannot_teleport")));
                                        $player->teleport($to->getWorld()->getSpawnLocation());
                                }
                        }
                }
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerInteractEvent $event
         */
        public function onPlayerInteract(PlayerInteractEvent $event){
                if($event->getBlock() instanceof ItemFrame && !$event->getPlayer()->hasPermission("command.framedrop")){
                        $event->cancel();
                }

                $tile = $event->getBlock()->getPosition()->getWorld()->getTile($event->getBlock()->getPosition());
                if($tile instanceof EnderChest){
                        $event->cancel();
                }
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerChatEvent $event
         */
        public function onPlayerChat(PlayerChatEvent $event){
                $player = $event->getPlayer();
                $recipients = $event->getRecipients();

                foreach($recipients as $index => $recipient){
                        if($recipient instanceof Player){
                                if($this->core->module_loader->fun_box->isIgnoring($recipient, $player)){
                                        unset($recipients[$index]);
                                }
                        }
                }
                $event->setRecipients($recipients);
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerJoinEvent $event
         */
        public function onPlayerJoin(PlayerJoinEvent $event){
                $player = $event->getPlayer();

                if(!$this->core->module_loader->ranks->isPlayerRegistered($player)){
                        $this->core->module_loader->ranks->registerPlayer($player);
                        $this->core->module_loader->economy->addNewPlayer($player);
                        $this->core->module_loader->credits->addNewPlayer($player);
                }

                $settings = $this->core->module_loader->player_data->getPlayerSettings($player);
                $player->setNameTag($this->core->module_loader->chat->formatDisplayName($player));

                if($settings->get(Settings::SETTING_TELEPORT_ON_JOIN, true)){
                        $player->teleport($this->core->module_loader->teleport->getSpawn());
                        $this->core->setIsInLobby($player);
                }

                if($settings->get(Settings::SETTING_ENABLE_HUD, true)){
                        $this->core->module_loader->hud->addPlayer($player);
                }

                $player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20, 10));
                $this->core->module_loader->entity_linker->sendRiderLinks($player);

                $event->setJoinMessage(RandomUtils::colorMessage(str_replace("@player", $player->getName(), $this->core->getMessage("player", "join"))));

                $title = $this->core->getMessage("welcome", "title");
                $subtitle = $this->core->getMessage("welcome", "subtitle");

                $this->core->getScheduler()->scheduleDelayedTask(new WelcomePlayerTask($this->core, $title, $subtitle, $player), 20);
                $this->core->module_loader->levels->startPlayTime($player);
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerQuitEvent $event
         */
        public function onPlayerQuit(PlayerQuitEvent $event){
                $player = $event->getPlayer();

                $this->core->module_loader->entity_linker->tryUnlink($player);

                if($this->core->module_loader->hud->inPlayers($player)){
                        $this->core->module_loader->hud->removePlayer($player);
                }

                if($this->core->module_loader->fun_box->isLSDEnabled($player)){
                        $this->core->module_loader->fun_box->disableLSD($player);
                }

                if($this->core->module_loader->fun_box->isGodEnabled($player)){
                        $this->core->module_loader->fun_box->disableGod($player);
                }

                $event->setQuitMessage(RandomUtils::colorMessage(str_replace("@player", $player->getName(), $this->core->getMessage("player", "leave"))));
                $this->core->module_loader->levels->endPlayTime($player);
        }

        /**
         * @priority LOWEST
         *
         * @param CommandEvent $event
         */
        public function onCommand(CommandEvent $event){
                $player = $event->getSender();
                $command = $event->getCommand();

                $receiver = null;

                foreach($player->getServer()->getOnlinePlayers() as $onlinePlayer){
                        $name = strtolower($onlinePlayer->getName());

                        if(stripos(strtolower($command), $name) !== false){
                                $command = str_ireplace($name, "\"$name\"", $command);
                                $event->setCommand($command);

                                $receiver = $onlinePlayer;
                                break;
                        }
                }

                if($player instanceof Player && $receiver instanceof Player){
                        if($this->core->module_loader->fun_box->isIgnoring($receiver, $player)){
                                $event->cancel();
                        }
                }

                if($player->hasPermission("command.cooldown.bypass")) return;

                foreach($this->command_cooldown_config as $cooldown){
                        if(stripos($command, $cooldown["command"]) !== false){
                                if($cooldown["type"] === "global"){
                                        $time = $this->command_cooldown["global"][$command] ?? null;
                                        if($time !== null){
                                                if(($time - time()) > 0){
                                                        $message = $cooldown["message"];
                                                        $message = str_replace("@time", ($time - time()), $message);
                                                        $player->sendMessage(RandomUtils::colorMessage($message));

                                                        $event->cancel();
                                                }else{
                                                        $this->command_cooldown["global"][$command] = time() + $cooldown["cooldown"];
                                                }
                                        }else{
                                                $this->command_cooldown["global"][$command] = time() + $cooldown["cooldown"];
                                        }
                                }elseif($cooldown["type"] === "player"){
                                        $time = $this->command_cooldown[$player->getName()][$command] ?? null;
                                        if($time !== null){
                                                if(($time - time()) > 0){
                                                        $message = $cooldown["message"];
                                                        $message = str_replace("@time", ($time - time()), $message);
                                                        $player->sendMessage(RandomUtils::colorMessage($message));

                                                        $event->cancel();
                                                }else{
                                                        $this->command_cooldown[$player->getName()][$command] = time() + $cooldown["cooldown"];
                                                }
                                        }else{
                                                $this->command_cooldown[$player->getName()][$command] = time() + $cooldown["cooldown"];
                                        }
                                }
                        }
                }
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerDeathEvent $event
         */
        public function onPlayerDeath(PlayerDeathEvent $event){
                $player = $event->getEntity();
                $cause = $event->getEntity()->getLastDamageCause();

                if($cause !== null){
                        $causeId = $cause->getCause();

                        $message = [
                            "contact",
                            "entity_attack",
                            "projectile",
                            "suffocation",
                            "fall",
                            "fire",
                            "fire_tick",
                            "lava",
                            "drowning",
                            "block_explosion",
                            "entity_explosion",
                            "void",
                            "suicide",
                            "magic",
                            "custom",
                            "starvation"
                        ];

                        $message = $this->core->getMessage("death_messages", $message[$causeId]);
                        $message = $message[array_rand($message)];

                        switch($causeId){
                                case EntityDamageEvent::CAUSE_CONTACT:
                                        if($cause instanceof EntityDamageByBlockEvent){
                                                $block = $cause->getDamager()->getName();
                                                $message = str_replace("@block", $block, $message);
                                        }
                                        break;
                                case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                                        if($cause instanceof EntityDamageByEntityEvent){
                                                $killer = $cause->getDamager();

                                                if($killer instanceof Player){
                                                        $message = $this->core->getMessage("death_messages", "player_attack");
                                                        $message = $message[array_rand($message)];

                                                        $item = $killer->getInventory()->getItemInHand()->getName();
                                                        $message = str_replace(["@entity", "@item"], [$killer->getName(), $item], $message);
                                                }else{
                                                        $message = str_replace("@entity", $killer->getNameTag(), $message);
                                                }
                                        }
                                        break;
                                case EntityDamageByEntityEvent::CAUSE_PROJECTILE:
                                        if($cause instanceof EntityDamageByChildEntityEvent){
                                                $killer = $cause->getDamager();

                                                if($killer instanceof Player){
                                                        /** @var array $message */
                                                        $message = $this->core->getMessage("death_messages", "player_projectile");
                                                        $message = $message[array_rand($message)];

                                                        $item = $killer->getInventory()->getItemInHand()->getName();
                                                        $message = str_replace(["@entity", "@item"], [$killer->getName(), $item], $message);
                                                }else{
                                                        $message = str_replace("@entity", $killer->getNameTag(), $message);
                                                }
                                        }
                                        break;
                        }
                        $message = str_replace("@player", $player->getName(), $message);
                        $event->setDeathMessage(RandomUtils::colorMessage($message));
                }else{
                        $event->setDeathMessage("");
                }
        }

        /**
         * @priority LOWEST
         *
         * @param EntityDamageEvent $event
         */
        public function onEntityDamage(EntityDamageEvent $event){
                $target = $event->getEntity();
                $cause = $event->getEntity()->getLastDamageCause();

                self::$damage_times[$target->getId()] = time();

                if($event->getCause() === EntityDamageEvent::CAUSE_FALL && isset($this->fall_protection[spl_object_hash($target)])){
                        unset($this->fall_protection[spl_object_hash($target)]);
                        $event->cancel();
                }

                if($event instanceof EntityDamageByEntityEvent){
                        $killer = $event->getDamager();

                        if($killer instanceof Player && $target instanceof Painting && !$killer->hasPermission("painting.nodrop")){
                                $event->cancel();
                        }

                        if($killer instanceof Player && $target instanceof Player){
                                $item = $killer->getInventory()->getItemInHand();
                                $settings = $this->core->module_loader->player_data->getPlayerSettings($target);

                                if($item->getNamedTag()->getByte("stack_stick", 0) !== 0 && $settings->get(Settings::SETTING_AFFECTED_BY_STACK_STICK, true)){
                                        $this->core->module_loader->entity_linker->linkRider($target, $killer);
                                        $event->cancel();
                                }elseif($item->getNamedTag()->getByte("ride_stick", 0) !== 0 && $settings->get(Settings::SETTING_AFFECTED_BY_RIDE_STICK, true)){
                                        $this->core->module_loader->entity_linker->linkRider($killer, $target);
                                        $event->cancel();
                                }
                        }
                }

                if($target instanceof Player && $this->core->module_loader->fun_box->isGodEnabled($target)){
                        $event->cancel();
                        return;
                }

                if($cause instanceof EntityDamageByEntityEvent){
                        $killer = $cause->getDamager();

                        if($killer instanceof Player and $target instanceof Player){
                                $event->setAttackCooldown((int) $this->core->getConfig()->get("attack_cooldown"));
                        }

                        if($killer instanceof Player && $target instanceof Living and ($event->getFinalDamage() >= $target->getHealth())){
                                if($target instanceof Player && $target->hasPermission("xp.nodrop")){
                                        return;
                                }
                                $killer->getXpManager()->addXp($target->getXpDropAmount());
                                if(method_exists($target, "setCurrentTotalXp")){
                                        $target->setCurrentTotalXp(0);
                                }
                        }
                }
        }

        /**
         * @priority LOWEST
         *
         * @param PlayerPreLoginEvent $event
         */
        public function onPlayerLogin(PlayerPreLoginEvent $event){
                $player = $event->getPlayerInfo()->getUsername();
                $server = $this->core->getServer();

                if($server->getNameBans()->isBanned($player)){
                        $banEntry = $server->getNameBans()->getEntry($player);
                        $date = $banEntry->getExpires();

                        $message = "&l&4[!] " . $player . " &chas been banned by &4" . $banEntry->getSource() . "&c.";

                        if($date === null){
                                $message .= " Time: &4forever&f.";
                                $message .= " Reason: &4{$banEntry->getReason()}&f.";
                        }else{
                                $diff = $date->diff(new \DateTime());

                                $times = [];
                                $stamps = [
                                    "y" => "years",
                                    "m" => "months",
                                    "d" => "days",
                                    "h" => "hours",
                                    "i" => "minutes",
                                    "s" => "seconds"
                                ];

                                foreach($stamps as $char => $stamp){
                                        if($diff->{$char} > 0){
                                                $times[] = "&4" . $diff->{$char} . " &c" . $stamp;
                                        }
                                }

                                $message .=" Time: &4" . implode(", ", $times) . ".";
                                $message .= $banEntry->getReason() !== "" ? " Reason: &4{$banEntry->getReason()}&f." : "";
                        }

                        $event->setKickReason(PlayerPreLoginEvent::KICK_REASON_PLUGIN, RandomUtils::colorMessage($message));
                }
        }

        /**
         * @priority HIGHEST
         *
         * @param BlockBreakEvent $event
         *
         * @throws \InvalidStateException
         */
        public function onBlockBreak(BlockBreakEvent $event){
                $player = $event->getPlayer();
                if($player->getGamemode() === GameMode::SURVIVAL() && !$event->isCancelled()){
                        $block = $event->getBlock();
                        if($this->core->module_loader->land_protector->isInMine($block->getPosition())){
                                $drops = $event->getDrops();

                                $world = $player->getWorld()->getDisplayName();
                                $name = $player->getName();

                                $itemInHand = $player->getInventory()->getItemInHand();

                                if(isset($this->auto_sell_worlds[$world]) && $this->auto_sell_worlds[$world] === true){
                                        foreach($drops as $drop){
                                                if(StaticManagement::getItemAbility($itemInHand) === StaticManagement::ABILITY_MONEY_BOOST){


                                                        $drop->setCount($drop->getCount() * 2);
                                                        $abilityDuration = StaticManagement::getItemAbilityDuration($itemInHand) - 1;

                                                        if($abilityDuration > 0){
                                                                StaticManagement::setItemAbilityDuration($itemInHand, $abilityDuration);
                                                                $item = StaticManagement::updateAbilityDescription($itemInHand, StaticManagement::ABILITY_MONEY_BOOST, StaticManagement::getItemAbilityDuration($itemInHand));
                                                        }elsE{
                                                                $item = StaticManagement::setItemAbilityInactive($itemInHand);
                                                        }

                                                        $player->getInventory()->setItemInHand($item);
                                                }elseif($this->core->module_loader->power_ups->isPowerUpActive($player, PowerUps::POWER_UP_MINING_TRIPLE)){
                                                        $drop->setCount($drop->getCount() * 3);
                                                }elseif($this->core->module_loader->power_ups->isPowerUpActive($player, PowerUps::POWER_UP_MINING)){
                                                        $drop->setCount($drop->getCount() * 2);
                                                }

                                                $price = SellCommand::$prices[$drop->getId() . ":" . $drop->getMeta()] ?? 0;
                                                if($price > 0){
                                                        $price = $price * $drop->getCount();
                                                }
                                                $money = $this->core->module_loader->economy->getCash($player);

                                                if(!isset($this->sell_log[$name])){
                                                        $this->sell_log[$name][0] = $money;
                                                        $this->sell_log[$name][1] = $price;
                                                        $this->sell_log_timer[$name] = time();
                                                }

                                                if((time() - $this->sell_log_timer[$name]) <= 1){
                                                        $this->sell_log[$name][1] += $price;
                                                }else{
                                                        $this->sell_log[$name][0] = $money;
                                                        $this->sell_log[$name][1] = $price;
                                                }
                                                $this->sell_log_timer[$name] = time();

                                                if($price > 0){
                                                        $message = $this->core->getMessage("sell_all", "sold_tip");
                                                        $message = str_replace(["@money", "@price"], [$this->sell_log[$name][0], $this->sell_log[$name][1]], $message);

                                                        $player->sendTip(RandomUtils::colorMessage($message));
                                                        $this->core->module_loader->economy->withdraw($player, $price);
                                                }
                                        }
                                }else{
                                        foreach($drops as $drop){
                                                if(StaticManagement::getItemAbility($itemInHand) === StaticManagement::ABILITY_MONEY_BOOST){
                                                        $drop->setCount($drop->getCount() * 2);

                                                        StaticManagement::setItemAbilityDuration($itemInHand, StaticManagement::getItemAbilityDuration($itemInHand) - 1);
                                                        $item = StaticManagement::updateAbilityDescription($itemInHand, StaticManagement::ABILITY_MONEY_BOOST, StaticManagement::getItemAbilityDuration($itemInHand));

                                                        $player->getInventory()->setItemInHand($item);
                                                }elseif($this->core->module_loader->power_ups->hasPowerUp($player, PowerUps::POWER_UP_MINING_TRIPLE)){
                                                        $drop->setCount($drop->getCount() * 3);
                                                }elseif($this->core->module_loader->power_ups->hasPowerUp($player, PowerUps::POWER_UP_MINING)){
                                                        $drop->setCount($drop->getCount() * 2);
                                                }

                                                if(!$player->getInventory()->canAddItem($drop)){
                                                        $player->sendPopup(RandomUtils::colorMessage("&l&8» &4INVENTORY FULL"));
                                                        $event->cancel();
                                                        return;
                                                }

                                                $player->getInventory()->addItem($drop);
                                        }
                                }
                                $event->setDrops([]);
                        }
                        $player->getXpManager()->addXp($event->getXpDropAmount());
                        $event->setXpDropAmount(0);
                }
        }
}