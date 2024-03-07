<?php
namespace PrestigeSociety\Teleport\Commands\Home;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\Teleport\Teleport;
class MaxHomesCommand extends CoreCommand{
        /**
         * MaxHomesCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.maxhomes");
                parent::__construct($plugin, "maxhomes", "Set or get a player's max homes", RandomUtils::colorMessage("&eUsage: /maxhomes <name> [amount]"), []);
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

                if(count($args) <= 0){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $player = $args[0];
                $amount = $args[1] ?? null;

                if(!$this->core->module_loader->levels->playerExists($player)){
                        $sender->sendMessage(RandomUtils::colorMessage("&l&4[!] $player &cdoes not exist in this server."));
                        return false;
                }

                $settings = $this->core->module_loader->player_data->getPlayerSettings($player);
                $max_homes = $settings->get(Settings::SETTING_MAX_HOMES, Teleport::DEFAULT_MAX_HOMES);

                if($amount === null){
                        $sender->sendMessage(RandomUtils::colorMessage("&l&2[!] $player's &amax homes: &2$max_homes"));
                        return true;
                }

                $amount = StringUtils::parseStringEquation($amount, $max_homes);

                $settings->set(Settings::SETTING_MAX_HOMES, $amount);
                $sender->sendMessage(RandomUtils::colorMessage("&l&2[!] &aSet &2$player's &amax homes to &2$amount"));

                return true;
        }
}