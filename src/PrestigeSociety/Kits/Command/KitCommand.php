<?php
namespace PrestigeSociety\Kits\Command;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Kits\Kits;
class KitCommand extends CoreCommand{
        /**
         * KitCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "kit", "Equip a kit", RandomUtils::colorMessage("&eUsage: /kit <name>"), []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         *
         * @throws Exception
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPlayer($sender)){
                        return false;
                }

                /** @var Player $sender */

                $kit = $args[0] ?? null;
                if($kit !== null){
                        if(!$this->core->module_loader->kits->kitExists($kit)){
                                $message = $this->core->getMessage("kits", "unknown_kit");
                                $message = str_replace("@kit", $kit, $message);

                                $sender->sendMessage(RandomUtils::colorMessage($message));
                                return false;
                        }

                        if(!$sender->hasPermission("kit.$kit")){
                                $message = $this->core->getMessage("kits", "no_permission");
                                $message = str_replace("@kit", $kit, $message);

                                $sender->sendMessage(RandomUtils::colorMessage($message));
                                return false;
                        }

                        if(in_array("auto", $args)){
                                $claim_output = $this->core->module_loader->kits->claimKit($sender, $kit);

                                $timestamp_string = ["@days", "@hours", "@minutes", "@seconds"];
                                $timestamp_values = $this->core->module_loader->kits->getCoolDownDHMS($sender, $kit);

                                if($claim_output === Kits::CLAIM_SUCCESS){
                                        $message = $this->core->getMessage("kits", "claim_message");
                                        $message = str_replace($timestamp_string, $timestamp_values, $message);
                                }elseif($claim_output === Kits::CLAIM_NO_ITEMS){
                                        $message = $this->core->getMessage("kits", "no_items");
                                }elseif($claim_output === Kits::CLAIM_NO_SPACE){
                                        $message = $this->core->getMessage("kits", "no_space");
                                }else{
                                        $message = $this->core->getMessage("kits", "cooldown");
                                        $message = str_replace($timestamp_string, $timestamp_values, $message);
                                }

                                $message = str_replace("@kit", $kit, $message);
                                $sender->sendMessage(RandomUtils::colorMessage($message));

                                return false;
                        }

                        if(in_array("view", $args)){
                                $this->core->module_loader->kits->openViewInventory($sender, $kit);
                                return true;
                        }

                        $this->core->module_loader->kits->openClaimInventory($sender, $kit);
                        return true;
                }

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->kits->CHOOSE_KIT_ID, $sender);
                return true;
        }
}