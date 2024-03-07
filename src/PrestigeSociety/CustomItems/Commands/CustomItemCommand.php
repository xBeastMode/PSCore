<?php
namespace PrestigeSociety\CustomItems\Commands;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
class CustomItemCommand extends CoreCommand{
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "customitem", "Give custom items", "Usage: /customitem <player> <name> <data-value> [count]", []);
                $this->setPermission("command.customitems");
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

                $player = $args[0] ?? null;
                $name = $args[1] ?? null;
                $dataValue = $args[2] ?? null;
                $count = $args[3] ?? 1;

                if($player === null || $name === null || $dataValue === null){
                        $sender->sendMessage(TextFormat::RED . $this->getUsage());
                        return false;
                }

                $player = $this->core->getServer()->getPlayerByPrefix($player);
                if($player === null){
                        $sender->sendMessage(TextFormat::RED . "Player is offline.");
                        return false;
                }

                if(!$this->core->module_loader->custom_items->addCustomItem($player, $name, $count, $dataValue)){
                        $sender->sendMessage(TextFormat::RED . "Failed to find custom item.");
                        return false;
                }

                $sender->sendMessage(TextFormat::GREEN . "Successfully gave {$player->getName()} custom item $name (x$count) with data value $dataValue.");
                return true;
        }
}