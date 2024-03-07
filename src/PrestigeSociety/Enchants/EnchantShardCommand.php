<?php

namespace PrestigeSociety\Enchants;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class EnchantShardCommand extends CoreCommand{
        /**
         * EnchantShardCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->setPermission("command.enchantshard");
                parent::__construct($core, "enchshard", "Give players enchant shards", RandomUtils::colorMessage("&eUsage: /enchant <player> <type> [index] [amount]"), []);
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
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $player = $args[0];

                $player = $sender->getServer()->getPlayerByPrefix($player);
                if($player === null){
                        $sender->sendMessage(TextFormat::RED . "Player is offline.");
                        return false;
                }

                $type = strtolower($args[1]);
                $index = $args[2] ?? 0;
                $amount = $args[3] ?? 1;

                $index = (int) $index;
                $types = ["replicate", "apply", "remove"];

                if(!in_array($type, $types)){
                        $sender->sendMessage(TextFormat::RED . "Unknown type. It must be: " . implode(", ", $types));
                        return false;
                }

                $shard = $this->core->module_loader->enchants->getShard($type, $index, $amount);
                if($shard === null){
                        $sender->sendMessage(TextFormat::RED . "Could not create shard of type $type with index $index");
                        return false;
                }

                $player->getInventory()->addItem($shard);
                $sender->sendMessage(TextFormat::GREEN . "Successfully added shard of type $type with index $index and amount of $amount");

                return true;
        }

}