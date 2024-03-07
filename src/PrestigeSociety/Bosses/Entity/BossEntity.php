<?php
namespace PrestigeSociety\Bosses\Entity;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\player\Player;
use pocketmine\world\Explosion;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Task\HurtPlayerTask;
use PrestigeSociety\Core\Utils\ConsoleUtils;
use PrestigeSociety\Core\Utils\Physics;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Levels\StaticLevels;
use PrestigeSociety\Management\StaticManagement;
use PrestigeSociety\PowerUps\PowerUps;
class BossEntity extends Human{
        public const EXPLODE_SIZE = 2;
        public const MAX_TARGET_DISTANCE = 10;

        /** @var string|null */
        public ?string $name = null;

        protected Location $origin;

        /** @var Player[] */
        protected array $boss_bar_players = [];

        /** @var array */
        protected array $damage_data = [];

        /** @var float */
        protected $scale = 2;
        /** @var bool */
        protected bool $ability_used = false;

        /** @var int */
        protected int $damage = 0;
        /** @var int */
        protected int $additional_damage = 0;
        /** @var array */
        protected array $powers = [];

        /** @var Entity|null */
        protected ?Entity $goal = null;
        /** @var bool */
        protected bool $attacking = false;

        /** @var bool */
        public bool $respawns = false;
        /**
         * BossEntity constructor.
         *
         * @param Location    $location
         * @param Skin        $skin
         * @param CompoundTag $nbt
         * @param array       $buffs
         * @param int         $damage
         * @param int         $health
         * @param bool        $respawns
         */
        public function __construct(Location $location, Skin $skin, CompoundTag $nbt, array $buffs = [], int $damage = 2, int $health = 1000, bool $respawns = false){
                parent::__construct($location, $skin, $nbt);

                $this->setHealth($health);
                $this->setMaxHealth($health);

                $this->origin = $location;
                $this->damage = $damage;
                $this->powers = $buffs;
                $this->respawns = false;

                $this->setCanSaveWithChunk(false);
        }

        /**
         * @param string $name
         */
        public function setName(string $name){
                $this->name = $name;
        }

        /**
         * @return Entity|null
         */
        public function getGoal(): ?Entity{
                return $this->goal = $this->findNearestTarget();
        }

        /**
         * @return Player|null
         */
        public function findNearestTarget(): ?Player{
                $viewers = array_filter($this->getViewers(), function (Player $viewer){
                        return $viewer->getLocation()->distance($this->getLocation()) <= self::MAX_TARGET_DISTANCE;
                });
                return count($viewers) > 0 ? $this->goal = $viewers[array_rand($viewers)] : null;
        }

        /**
         * @param float $amount
         * @param int   $cause
         *
         * @return float
         */
        public function exhaust(float $amount, int $cause = PlayerExhaustEvent::CAUSE_CUSTOM): float{
                return 0;
        }

        /**
         * @param int $tickDiff
         */
        public function doFoodTick(int $tickDiff = 1): void{
        }

        /**
         * @param int $tickDiff
         *
         * @return bool
         */
        public function doOnFireTick(int $tickDiff = 1): bool{
                $this->extinguish();
                return false;
        }

        protected function updateFallState(float $distanceThisTick, bool $onGround): ?float{
                return null;
        }

        /**
         * @param Player $player
         * @param int    $damage
         */
        public function setDamage(Player $player, int $damage){
                if(!isset($this->damage_data[spl_object_hash($player)])){
                        if(($this->getHealth() - $damage) <= 0){
                                $this->damage_data[spl_object_hash($player)] = [$player, $this->getHealth()];
                        }else{
                                $this->damage_data[spl_object_hash($player)] = [$player, $damage];
                        }
                }else{
                        if(($this->getHealth() - $damage) <= 0){
                                $this->damage_data[spl_object_hash($player)][1] += $this->getHealth();
                        }else{
                                $this->damage_data[spl_object_hash($player)][1] += $damage;
                        }
                }
        }

        /**
         * @param Player $player
         */
        public function addBossBar(Player $player){
                $position = $this->getLocation()->round();
                $packet = new BossEventPacket();
                $packet->bossActorUniqueId = $this->id;
                $packet->eventType = BossEventPacket::TYPE_SHOW;
                $packet->healthPercent = 100;
                $packet->darkenScreen = true;
                $packet->color = 1;
                $packet->overlay = 1;
                $packet->title = RandomUtils::colorMessage("&l&e" . $this->name .  " &f- &4❤ &c" . $this->getHealth() . " &f- &elocation: &c{$position->x}, {$position->y}, {$position->z}");
                $player->getNetworkSession()->sendDataPacket($packet);
        }

        /**
         * @param Player $player
         */
        public function updateBossBar(Player $player){
                $position = $this->getLocation()->round();
                $packet = new BossEventPacket();
                $packet->bossActorUniqueId = $this->id;
                $packet->eventType = BossEventPacket::TYPE_TITLE;
                $packet->healthPercent = (int)(($this->getHealth() / $this->getMaxHealth()) * 100);
                $packet->darkenScreen = true;
                $packet->color = 1;
                $packet->overlay = 1;
                $packet->title = RandomUtils::colorMessage("&l&e" . $this->name .  " &f- &4❤ &c" . $this->getHealth() . " &f- &elocation: &c{$position->x}, {$position->y}, {$position->z}");
                $player->getNetworkSession()->sendDataPacket($packet);
        }

        /**
         * @param Player $player
         */
        public function removeBossBar(Player $player){
                $packet = new BossEventPacket();
                $packet->bossActorUniqueId = $this->id;
                $packet->eventType = BossEventPacket::TYPE_HIDE;
                $player->getNetworkSession()->sendDataPacket($packet);
        }

        public function sendBossBar(){
                foreach($this->boss_bar_players as $index => $player){
                        if(!$player->isConnected()){
                                unset($this->boss_bar_players[$index]);
                        }
                }

                foreach($this->boss_bar_players as $index => $player){
                        if($player->getWorld() !== $this->getWorld()){
                                unset($this->boss_bar_players[$index]);
                                $this->removeBossBar($player);
                        }
                }
                foreach($this->getWorld()->getPlayers() as $player){
                        if(!isset($this->boss_bar_players[spl_object_hash($player)])){
                                $this->boss_bar_players[spl_object_hash($player)] = $player;
                                $this->addBossBar($player);
                        }
                }
                foreach($this->boss_bar_players as $player) $this->updateBossBar($player);
        }

        public function onDespawn(){
        }

        protected function onDispose(): void{
                $this->onDespawn();
                parent::onDispose();
        }

        public function onDeath(): void{
                $this->onDespawn();

                if($this->name === null){
                        parent::onDeath();
                        return;
                }

                $explosion = new Explosion($this->getLocation(), self::EXPLODE_SIZE);
                $explosion->explodeB();

                RandomUtils::playSound("mob.enderdragon.death", $this);

                if(count($this->damage_data) > 0){
                        $this->death();
                }

                parent::onDeath();
        }

        public function fight(): void{
                $pk = new AnimatePacket();
                $pk->actorRuntimeId = $this->getId();
                $pk->action = AnimatePacket::ACTION_SWING_ARM;
                $this->getWorld()->broadcastPacketToViewers($this->getLocation(), $pk);

                if($this->goal !== null && $this->goal instanceof Entity){
                        $this->goal->attack(new EntityDamageByEntityEvent($this, $this->goal, EntityDamageByEntityEvent::CAUSE_ENTITY_ATTACK, $this->damage, [], 0.4));
                }
        }

        /**
         * @return bool
         */
        public function navigate(): bool{
                if($this->getLocation()->getY() <= 1){
                        $this->teleport($this->getWorld()->getSafeSpawn($this->origin));
                }

                $goal = $this->getGoal();
                if($goal === null || $goal->getWorld() !== $this->getWorld()) return false;
                [$yaw, $pitch] = Physics::calculateRotationEulerAngle($this->location, $goal->location);

                if($goal instanceof Living && $goal->location->distance($this->location) <= 2){
                        $this->randomEffects($goal);
                        return true;
                }

                $this->location->yaw = $yaw;
                $this->location->pitch = $pitch;

                $this->motion = Physics::calculateMotionVector($this->location, $goal->location, 0.5);

                if($this->isCollidedHorizontally){
                        $this->jumpVelocity = 2;
                        $this->jump();
                }else{
                        $this->motion->y = -1;
                }

                return false;
        }

        public function updateCharacteristics(){
                $this->setNameTag(RandomUtils::colorMessage("&l&e" . $this->name . "\n&4❤ &c" . $this->getHealth()));
                $this->sendBossBar();
        }

        /**
         * @param int $currentTick
         *
         * @return bool
         */
        public function onUpdate(int $currentTick = 1): bool{
                $this->sendBossBar();
                $this->ability();
                $this->updateCharacteristics();

                if($this->navigate() && $this->ticksLived % 4 === 0){
                        $this->attacking = true;
                        $this->fight();
                }else{
                        $this->attacking = false;
                }

                return parent::onUpdate($currentTick);
        }

        protected function ability(){
                if($this->ticksLived % 5 === 0 && $this->scale < 3 && $this->ability_used){
                        $this->setScale($this->scale += 1);
                }

                if($this->ability_used) return;

                if((($this->getHealth() / $this->getMaxHealth()) * 100) < 10){
                        $this->setHealth($this->getHealth() + (($this->getMaxHealth() / 100) * 10));
                        $this->additional_damage = $this->damage / 2;

                        RandomUtils::playSound("mob.enderdragon.growl", $this, 500);
                        foreach($this->getViewers() as $entity){
                                $motion = $entity->getDirectionVector();

                                $motion->multiply(-2);
                                $motion->y = 2;

                                $entity->setMotion($motion);

                                if($entity instanceof Player){
                                        PrestigeSocietyCore::getInstance()->getScheduler()->scheduleRepeatingTask(new HurtPlayerTask($entity, 10, 1), 1);
                                }
                        }

                        $this->ability_used = true;
                }
        }

        /**
         * @param Living $entity
         */
        public function randomEffects(Living $entity){
                if(count($this->powers) > 0){
                        $rand = array_rand($this->powers[0]);

                        $sound = $this->powers[0][$rand];
                        $effect = $this->powers[1][$rand] ?? $this->powers[1][array_rand($this->powers[1])];

                        if(RandomUtils::randomFloat(0, 100) <= (float)$effect[0]){
                                RandomUtils::playSound($sound[0], $this, (int)$sound[1], (float)$sound[2]);

                                if(!$effect[1] instanceof EffectInstance){
                                        if(is_array($effect[1])){
                                                foreach($effect[1] as $command){
                                                        $command = str_replace(
                                                            ["@x", "@y", "@z", "@level", "@players", "@eid"],
                                                            [$entity->location->x, $entity->location->y, $entity->location->z, $entity->getWorld()->getDisplayName(), implode(",", $this->getViewers()), $this->getId()],
                                                            $command);
                                                        if($entity instanceof Player) $command = str_replace("@player", $entity->getName(), $command);

                                                        ConsoleUtils::dispatchCommandAsConsole($command);
                                                }
                                        }else{
                                                eval($effect[1]);
                                        }
                                }else{
                                        $entity->getEffects()->add($effect[1]);
                                }
                        }
                }
        }

        /**
         * @param EntityDamageEvent $source
         */
        public function attack(EntityDamageEvent $source): void{
                $health = $this->getHealth();
                parent::attack($source);
                if(!$source->isCancelled()){
                        $healthLost = $health - $this->getHealth();
                        if($source instanceof EntityDamageByEntityEvent){
                                $damager = $source->getDamager();
                                if($damager instanceof Player){
                                        $mod = PrestigeSocietyCore::getInstance()->module_loader;
                                        $combatTime = PrestigeSocietyCore::getInstance()->getConfig()->getAll()["combat_logger"]["time"];

                                        $mod->fun_box->disableGod($damager);
                                        $mod->fun_box->disableFlight($damager);
                                        $mod->fun_box->disableLSD($damager);
                                        $mod->combat_logger->checkTime($damager, $combatTime);

                                        $this->setDamage($damager, $healthLost);
                                }
                        }
                }
        }

        /**
         * @param Player $player
         *
         * @return int
         */
        protected function getDamage(Player $player): int{
                $percent = $this->damage_data[spl_object_hash($player)] ?? null;
                return $percent !== null ? $percent[1] : 0;
        }

        /**
         * @param Player $player
         * @param int    $percentage
         *
         * @return int
         */
        protected function getDamagePercent(Player $player, int $percentage = 100): int{
                $percentage_data = $this->damage_data[spl_object_hash($player)] ?? null;
                return $percentage_data !== null ? round(($percentage_data[1] / ($this->getMaxHealth())) * $percentage) : 0;
        }

        /**
         * @param Player $player
         * @param string $command
         *
         * @return string
         */
        protected function formatCommand(Player $player, string $command): string{
                $command = explode(" ", $command);
                $itemInHand = $player->getInventory()->getItemInHand();

                foreach($command as $key => $value){
                        $split = explode("/", $value);

                        if($split > 1){
                                switch(strtolower($split[0])){
                                        case "percent":
                                                $percent = $this->getDamagePercent($player, (int) $split[1]);
                                                if(StaticManagement::getItemAbility($itemInHand) === StaticManagement::ABILITY_DOUBLE_BOSS_REWARD){
                                                        $abilityDuration = StaticManagement::getItemAbilityDuration($itemInHand) - 1;

                                                        if($abilityDuration > 0){
                                                                StaticManagement::setItemAbilityDuration($itemInHand, $abilityDuration);
                                                                $item = StaticManagement::updateAbilityDescription($itemInHand, StaticManagement::ABILITY_DOUBLE_BOSS_REWARD, StaticManagement::getItemAbilityDuration($itemInHand));
                                                        }elsE{
                                                                $item = StaticManagement::setItemAbilityInactive($itemInHand);
                                                        }

                                                        $player->getInventory()->setItemInHand($item);
                                                        $percent *= 2;
                                                }elseif(PrestigeSocietyCore::getInstance()->module_loader->power_ups->isPowerUpActive($player, PowerUps::POWER_UP_BOSS)){
                                                        $percent *= 2;
                                                }
                                                $command[$key] = $percent;
                                                break;
                                }
                        }
                }

                return implode(" ", $command);
        }

        public function death(){
                $this->sendForm();

                $plugin = PrestigeSocietyCore::getInstance();
                $configurations = $plugin->module_configurations;
                $moduleLoader = $plugin->module_loader;

                /** @var Player $mostDamage */
                $mostDamage = null;
                $damage = PHP_INT_MIN;

                foreach($this->damage_data as $index => $damager){
                        $finalDamage = $damager[1];
                        /** @var Player $killer */
                        $killer = $damager[0];
                        if(!$killer->isOnline()){
                                unset($this->damage_data[$index]);
                                continue;
                        }

                        if($finalDamage > $damage){
                                $mostDamage = $killer;
                                $damage = $finalDamage;
                        }

                        if(!isset($configurations->bosses[$this->name])) return;
                        $boss_data = $configurations->bosses[$this->name];

                        foreach($boss_data["commands"] as $command){
                                ConsoleUtils::dispatchCommandAsConsole(str_replace(
                                    ["@player", "@players"],
                                    [$killer->getName(), implode(",", $this->getViewers())],
                                    $this->formatCommand($killer, $command)
                                ));
                        }
                }

                $participants = [];
                if($mostDamage !== null){
                        foreach($this->damage_data as $damager){
                                if($damager[0] !== $mostDamage) $participants[] = $damager[0];
                        }

                        $moduleLoader->events->onBossKill($this, $mostDamage, $participants);
                        StaticLevels::setBossesKilled($mostDamage, StaticLevels::getBossesKilled($mostDamage) + 1);
                }

                $message = $plugin->getMessage("bosses", "kill_message");
                $message = str_replace(
                    ["@name", "@player", "@damage", "@max", "@percent"],
                    [$this->name, $mostDamage->getName(), $damage, $this->getMaxHealth(), $this->getDamagePercent($mostDamage)],
                    $message);
                $this->getWorld()->getServer()->broadcastMessage(RandomUtils::colorMessage($message));
        }

        /**
         * @param Player|null $top
         */
        public function sendForm(Player $top = null){
                if($top === null){
                        $damage = PHP_INT_MIN;

                        foreach($this->damage_data as $damager){
                                $finalDamage = $damager[1];
                                /** @var Player $killer */
                                $killer = $damager[0];

                                if(!$killer->isOnline()) continue;

                                if($finalDamage > $damage){
                                        $top = $killer;
                                        $damage = $finalDamage;
                                }
                        }
                }

                if($top !== null){
                        $data = [[$top, $this->getDamage($top), $this->getDamagePercent($top), $this->getMaxHealth()], []];
                }else{
                        $data = [false, []];
                }

                $receivers = [];
                foreach($this->damage_data as $damager){
                        $receivers[] = $player = $damager[0];

                        /** @var Player $player */
                        if($player === $top || !$player->isOnline()){
                                continue;
                        }

                        $data[1][] = [$player, $this->getDamage($player), $this->getDamagePercent($player), $this->getMaxHealth()];
                }
                PrestigeSocietyCore::getInstance()->module_loader->form_manager->sendForm(PrestigeSocietyCore::getInstance()->module_loader->bosses->BOSS_RESULT_ID, $receivers, $data);
        }

        /**
         * @return array
         */
        public function getDrops(): array{
                return [];
        }

        /**
         * @return string
         */
        public function getName(): string{
                return $this->name;
        }
}