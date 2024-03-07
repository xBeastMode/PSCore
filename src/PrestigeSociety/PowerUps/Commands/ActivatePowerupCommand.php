<?php
namespace PrestigeSociety\PowerUps\Commands;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\command\CommandSender;
use PrestigeSociety\PowerUps\PowerUps;
class ActivatePowerupCommand extends CoreCommand{
        /**
         * ActivatePowerupCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.activatepowerup");
                parent::__construct($plugin,  "activatepowerup", "Allows you to activate a powerup for a player", "Usage: /activatepowerup <player> <power-up>", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }

                if(count($args) < 2){
                        $sender->sendMessage(TextFormat::GREEN . $this->getUsage());
                        return false;
                }

                $player = $args[0];
                $powerup = $args[1];

                if(!$this->core->module_loader->power_ups->powerUpExists($powerup)){
                        $sender->sendMessage(RandomUtils::colorMessage("&cInvalid power-up, must be: " . implode(", ", $this->core->module_loader->power_ups->getPowerUps())));
                        return false;
                }

                if(!$this->core->module_loader->power_ups->hasPowerUp($player, $powerup)){
                        $sender->sendMessage(RandomUtils::colorMessage("&cError: either $player is is not registered in our database or does not have a $powerup powerup."));
                        return false;
                }

                $this->core->module_loader->power_ups->setPowerUpActive($player, $powerup);

                $duration = round($this->core->module_loader->power_ups->getActivePowerUpTimeLeft($player, $powerup), 1);
                $sender->sendMessage(RandomUtils::colorMessage("&aActivated power-up ($powerup) for $player for $duration hours."));
                
                if(($player = $sender->getServer()->getPlayerByPrefix($player)) !== null){
                        $powerUps = [
                            PowerUps::POWER_UP_MINING => "double mine booster",
                            PowerUps::POWER_UP_MINING_TRIPLE => "triple mine booster",
                            PowerUps::POWER_UP_BOSS => "boss reward booster",
                            PowerUps::POWER_UP_FLIGHT => "flight power up",
                            PowerUps::POWER_UP_KEY_DROP => "crate key drop booster"
                        ];
                        
                        $message = $this->core->getMessage("power_ups", "activated");
                        $message = str_replace(["@name", "@time"], [$powerUps[$powerup], $duration], $message);

                        $player->sendMessage(RandomUtils::colorMessage($message));
                }

                return true;
        }
}