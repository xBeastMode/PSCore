<?php
namespace PrestigeSociety\PowerUps\Commands;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\command\CommandSender;
class AddPowerupCommand extends CoreCommand{
        /**
         * AddPowerupCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.addpowerup");
                parent::__construct($plugin,  "addpowerup", "Allows you to add a powerup to a player", "Usage: /addpowerup <player> <power-up> <duration-hours> [amount]", ["addpu"]);
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

                if(count($args) < 3){
                        $sender->sendMessage(TextFormat::GREEN . $this->getUsage());
                        return false;
                }

                $player = $args[0];
                $powerup = $args[1];

                if(!is_numeric($args[2]) || $args[2] <= 0){
                        $sender->sendMessage(RandomUtils::colorMessage("&cInvalid duration hours, must be numeric and greater than 0"));
                        $sender->sendMessage(TextFormat::GREEN . $this->getUsage());

                        return false;
                }

                if(isset($args[3]) && (!is_numeric($args[3]) || $args[3] <= 0)){
                        $sender->sendMessage(RandomUtils::colorMessage("&cInvalid amount, must be numeric and greater than 0"));
                        $sender->sendMessage(TextFormat::GREEN . $this->getUsage());

                        return false;
                }

                $duration = (int) $args[2];
                $amount = isset($args[3]) ? (int) $args[3] : 1;

                if(!$this->core->module_loader->power_ups->powerUpExists($powerup)){
                        $sender->sendMessage(RandomUtils::colorMessage("&cInvalid power-up, must be: " . implode(", ", $this->core->module_loader->power_ups->getPowerUps())));
                        return false;
                }

                for($i = 0; $i < $amount; $i++){
                        $this->core->module_loader->power_ups->setPowerUp($player, $powerup, $duration);
                }
                $sender->sendMessage(RandomUtils::colorMessage("&aAdded power-up ($powerup x$amount) to $player for $duration hours."));

                return true;
        }
}