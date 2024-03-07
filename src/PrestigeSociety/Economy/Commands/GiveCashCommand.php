<?php
namespace PrestigeSociety\Economy\Commands;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\command\CommandSender;
class GiveCashCommand extends CoreCommand{
        /**
         *
         * SetDeathsCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         *
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("economy.command.all;economy.command.givecash");
                parent::__construct($plugin,  "givecash", "Allows you to give a players cash", "Usage: /givecash <player> <amount>", []);
        }

        /**
         *
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         *
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender) && !$sender->hasPermission("economy.command.all")){
                        return false;
                }

                if(count($args) < 2){
                        $sender->sendMessage(TextFormat::GREEN . $this->getUsage());
                        return false;
                }

                $amount = intval($args[1]);
                if(!is_numeric($amount) or !is_int($amount)){
                        $sender->sendMessage(RandomUtils::colorMessage("&l&4[!] &cThe amount must be an integer."));
                        return false;
                }

                if($amount <= 0){
                        $sender->sendMessage(RandomUtils::colorMessage("&l&4[!] &cThe amount cannot be less than 1."));
                        return false;
                }
                
                $name = $args[0];
                if(($player = $this->core->getServer()->getPlayerByPrefix($name)) instanceof Player){
                        if($this->core->module_loader->economy->withdraw($player, $amount)){
                                $sender->sendMessage(RandomUtils::colorMessage("&l&2[!] &aGave &2{$player->getName()} $$amount &acash!"));

                                if(($player = $sender->getServer()->getPlayerByPrefix($name)) !== null){
                                        $message = $this->core->getMessage("economy", "add_cash_notification");
                                        $message = str_replace(["@player", "@money"], [$sender->getName(), $amount], $message);
                                        $player->sendMessage(RandomUtils::colorMessage($message));
                                }
                        }else{
                                $sender->sendMessage(RandomUtils::colorMessage("&l&4[!] &cFailed to give &4{$player->getName()} &ccash."));
                        }
                }else{
                        $sender->sendMessage(RandomUtils::colorMessage("&l&4[!] &cThat player is offline"));
                }
                return true;
        }
}