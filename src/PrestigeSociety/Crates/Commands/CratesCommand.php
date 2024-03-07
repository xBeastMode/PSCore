<?php
namespace PrestigeSociety\Crates\Commands;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
class CratesCommand extends CoreCommand{
        /**
         * CratesCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "crates", "Access your crates!", "Usage: /crates", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                $option = $args[0] ?? null;
                $player = $args[1] ?? null;
                $type = $args[2] ?? null;
                $amount = $args[3] ?? 1;

                if($option !== null && $this->testCustomPermission($sender, "command.crates")){
                        if($player === null || $type === null){
                                $sender->sendMessage(TextFormat::RED . "Usage: /crates <open|add|subtract> <player> <type> [amount]");
                                return false;
                        }

                        $player = $this->core->getServer()->getPlayerByPrefix($player);
                        if($player === null){
                                $sender->sendMessage(TextFormat::RED . "Player is offline.");
                                return false;
                        }

                        $option = strtolower($option);
                        switch($option){
                                case "open":
                                        $this->core->module_loader->crates->openCrate($player, $type, 0);
                                        $sender->sendMessage(TextFormat::GREEN . "Opened crate $type (x$amount) for {$player->getName()}.");
                                        break;
                                case "add":
                                        $this->core->module_loader->crates->addCrateCount($player, $type, $amount);
                                        $sender->sendMessage(TextFormat::GREEN . "Added crate $type (x$amount) for {$player->getName()}.");
                                        break;
                                case "subtract":
                                        $this->core->module_loader->crates->subtractCrateCount($player, $type, $amount);
                                        $sender->sendMessage(TextFormat::GREEN . "Subtracted crate $type (x$amount) for {$player->getName()}.");
                                        break;
                        }

                        return true;
                }

                if(!$this->testPlayer($sender)){
                        return false;
                }

                $options = ["check_permissions" => true, "message" => $this->core->getMessage("command_lock", "crates")];
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->crates->CRATES_ID, $sender, [], $options);
                return true;
        }
}