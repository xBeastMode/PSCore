<?php
namespace PrestigeSociety\PowerUps;
use JetBrains\PhpStorm\Pure;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\PowerUps\ActivatePowerUpsForm;
use PrestigeSociety\Forms\FormList\PowerUps\BuyPowerUpForm;
use PrestigeSociety\Forms\FormList\PowerUps\ConfirmPurchaseForm;
use PrestigeSociety\Forms\FormList\PowerUps\PowerUpsForm;
use PrestigeSociety\Forms\FormManager;
use PrestigeSociety\PowerUps\Task\PowerUpExpireTask;
class PowerUps{
        const POWER_UP_MINING = "mining";
        const POWER_UP_MINING_TRIPLE = "triple_mining";
        const POWER_UP_BOSS = "boss";
        const POWER_UP_FLIGHT = "flight";
        const POWER_UP_KEY_DROP = "key_drop";

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var array[][] */
        protected array $power_ups = [];

        /** @var array[] */
        protected array $cache = [];

        public int $POWER_UPS_ID = 0;
        public int $ACTIVATE_POWER_UPS_ID = 0;
        public int $BUY_POWER_UPS_ID = 0;
        public int $CONFIRM_PURCHASE_ID = 0;

        /**
         * PowerUps constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                if(!file_exists($this->core->getDataFolder() . "powerups/")){
                        mkdir($this->core->getDataFolder() . "powerups/");
                }

                $this->POWER_UPS_ID = FormManager::getNextFormId();
                $this->ACTIVATE_POWER_UPS_ID = FormManager::getNextFormId();
                $this->BUY_POWER_UPS_ID = FormManager::getNextFormId();
                $this->CONFIRM_PURCHASE_ID = FormManager::getNextFormId();

                $core->module_loader->form_manager->registerHandler($this->POWER_UPS_ID, PowerUpsForm::class);
                $core->module_loader->form_manager->registerHandler($this->ACTIVATE_POWER_UPS_ID, ActivatePowerUpsForm::class);
                $core->module_loader->form_manager->registerHandler($this->BUY_POWER_UPS_ID, BuyPowerUpForm::class);
                $core->module_loader->form_manager->registerHandler($this->CONFIRM_PURCHASE_ID, ConfirmPurchaseForm::class);

                $this->loadPowerUps();
                $this->core->getScheduler()->scheduleRepeatingTask(new PowerUpExpireTask($core), 20 * 60);
        }

        /**
         * @return array
         */
        public function getPowerUps(): array{
                return [static::POWER_UP_MINING, static::POWER_UP_MINING_TRIPLE, static::POWER_UP_BOSS, static::POWER_UP_FLIGHT, static::POWER_UP_KEY_DROP];
        }

        /**
         * @param string $name
         *
         * @return bool
         */
        #[Pure] public function powerUpExists(string $name): bool{
                return in_array($name, $this->getPowerUps());
        }

        /**
         * @return string
         */
        #[Pure] public function getPath(){
                return $this->core->getDataFolder() . "powerups/";
        }

        public function loadPowerUps(){
                chdir($this->getPath());
                foreach(glob("*.json", GLOB_BRACE) as $item){
                        $this->power_ups[substr($item, 0, -5)] = json_decode(file_get_contents($item), true);
                }
        }

        /**
         * @param null|string $name
         */
        public function savePowerUps(?string $name = null){
                $nameLC = strtolower($name ?? "");
                if($name !== null && isset($this->power_ups[$nameLC])){
                        file_put_contents($this->getPath() . $nameLC . ".json", json_encode($this->power_ups[$nameLC]));
                }else{
                        foreach($this->power_ups as $name => $v){
                                $this->savePowerUps($name);
                        }
                }
        }

        /**
         * @param string $name
         *
         * @return bool
         */
        public function deletePowerUps(string $name): bool{
                if(isset($this->power_ups[$name])){
                        unlink($this->getPath() . $name . ".json");
                        return true;
                }
                return false;
        }

        /**
         * @param        $player
         * @param string $name
         *
         * @return bool
         */
        #[Pure] public function hasPowerUp($player, string $name): bool{
                return isset($this->power_ups[strtolower(RandomUtils::getName($player))][$name]);
        }

        /**
         * @param        $player
         * @param string $name
         * @param int    $duration
         */
        public function setPowerUp($player, string $name, int $duration){
                $this->power_ups[strtolower(RandomUtils::getName($player))][$name][] = ["name" => $name, "duration" => $duration * 3600, "expires" => time() + ($duration * 3600), "active" => false];
                $this->savePowerUps($player);
        }

        /**
         * @param        $player
         * @param string $name
         *
         * @return bool
         */
        public function isPowerUpActive($player, string $name): bool{
                if(isset($this->cache[strtolower(RandomUtils::getName($player))][$name])) return true;
                if($this->hasPowerUp($player, $name)){
                        foreach($this->power_ups[strtolower(RandomUtils::getName($player))] as $powerUps){
                                foreach($powerUps as $powerUp){
                                        if($powerUp["active"] && $powerUp["name"] === $name){
                                                $this->cache[strtolower(RandomUtils::getName($player))][$name] = true;
                                                return true;
                                        }
                                }
                        }
                }
                return false;
        }

        /**
         * @param        $player
         * @param string $name
         * @param bool   $hours
         *
         * @return float
         */
        public function getActivePowerUpTimeLeft($player, string $name, bool $hours = true): float{
                if($this->hasPowerUp($player, $name)){
                        foreach($this->power_ups[strtolower(RandomUtils::getName($player))] as $powerUps){
                                foreach($powerUps as $powerUp){
                                        if($powerUp["active"] && $powerUp["name"] === $name){
                                                return $hours ? ($powerUp["expires"] - time()) / 3600 : $powerUp["expires"] - time();
                                        }
                                }
                        }
                }
                return 0;
        }

        /**
         * @param        $player
         * @param string $name
         *
         * @return bool
         */
        public function setPowerUpActive($player, string $name): bool{
                if($this->hasPowerUp($player, $name)){
                        $powerup = array_shift($this->power_ups[strtolower(RandomUtils::getName($player))][$name]);
                        $powerup["active"] = true;
                        $powerup["duration"] = time() + $powerup["duration"];

                        $this->power_ups[strtolower(RandomUtils::getName($player))][$name][] = $powerup;
                        $this->savePowerUps($player);
                        return true;
                }
                return false;
        }

        /**
         * @param        $player
         * @param string $name
         *
         * @return bool
         */
        public function removePowerUp($player, string $name): bool{
                if($this->hasPowerUp($player, $name)){
                        $array = $this->power_ups[strtolower(RandomUtils::getName($player))][$name];
                        array_shift($array);
                        if(count($array) <= 0){
                                unset($this->power_ups[strtolower(RandomUtils::getName($player))][$name]);
                        }
                        $this->savePowerUps($player);
                        return true;
                }
                return false;
        }

        /**
         * @param $player
         *
         * @return array
         */
        #[Pure] public function getPlayerPowerUps($player): array{
                return $this->power_ups[strtolower(RandomUtils::getName($player))] ?? [];
        }

        /**
         * @param string $player
         * @param array  $powerUpData
         */
        public function onExpire(string $player, array $powerUpData): void{
                $powerUps = [
                    PowerUps::POWER_UP_MINING => "double mine booster",
                    PowerUps::POWER_UP_MINING_TRIPLE => "triple mine booster",
                    PowerUps::POWER_UP_BOSS => "boss reward booster",
                    PowerUps::POWER_UP_FLIGHT => "flight power up",
                    PowerUps::POWER_UP_KEY_DROP => "crate key drop booster"
                ];

                $name = $powerUps[$powerUpData["name"]];
                $onlinePlayer = $this->core->getServer()->getPlayerByPrefix($player);

                if($onlinePlayer !== null){
                        $message = $this->core->getMessage("power_ups", "expired");
                        $message = str_replace("@name", $name, $message);
                        $onlinePlayer->sendMessage(RandomUtils::colorMessage($message));

                        if($powerUpData["name"] === PowerUps::POWER_UP_FLIGHT && !$onlinePlayer->hasPermission("pl.fly")){
                                $onlinePlayer->setAllowFlight(false);
                        }
                }

                unset($this->cache[strtolower(RandomUtils::getName($player))][$powerUpData["name"]]);
        }

        public function removeExpiredPowerUps(): void{
                foreach($this->power_ups as $name => $powerUps){
                        foreach($powerUps as $i => $powerUp){
                                foreach($powerUp as $j => $power){
                                        if(!$power["active"]){
                                                $this->power_ups[$name][$i][$j]["expires"] = time() + $power["duration"];
                                        }

                                        if($power["expires"] <= time() && $power["active"]){
                                                $this->onExpire($name, $power);
                                                unset($this->power_ups[$name][$i][$j]);
                                        }
                                }

                                if(count($this->power_ups[$name][$i]) <= 0){
                                        unset($this->power_ups[$name][$i]);
                                }
                        }

                        if(count($this->power_ups[$name]) <= 0){
                                $this->deletePowerUps($name);
                                unset($this->power_ups[$name]);
                        }
                }
                $this->savePowerUps();
        }
}