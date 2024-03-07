<?php
namespace PrestigeSociety\Teleport\Commands\Warp;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class WarpInfoCommand extends CoreCommand{
        /**
         * WarpInfoCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.warpinfo");
                parent::__construct($plugin, "warpinfo", "Get detailed information about warps", RandomUtils::colorMessage("&eUsage: /warpinfo"), []);
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

                $output = "===========================\n";
                $output .= implode("&f===========================\n", array_map(function (array $warp){
                        $output = "&l&8» &r&7name: &f{$warp["name"]}\n";
                        $output .= "&l&8» &r&7x: &f{$warp["x"]}\n";
                        $output .= "&l&8» &r&7y: &f{$warp["y"]}\n";
                        $output .= "&l&8» &r&7z: &f{$warp["z"]}\n";
                        $output .= "&l&8» &r&7level: &f{$warp["level"]}\n";
                        $output .= "&l&8» &r&7owner: &f{$warp["owner"]}\n";
                        $output .= "&l&8» &r&7created: &f" . date("M j, Y, g:ia", strtotime($warp["created_at"])) . "\n";
                        $output .= "&l&8» &r&7updated: &f" . date("M j, Y, g:ia", strtotime($warp["updated_at"])) . "\n";

                        return $output;
                }, $this->core->module_loader->teleport->warp_api->getWarps()));
                $output .= "===========================";

                if(!$this->testPlayer($sender)){
                        $sender->sendMessage(RandomUtils::colorMessage($output));
                        return true;
                }

                /** @var Player $sender */
                $form = $this->core->module_loader->form_manager->getFastSimpleForm($sender, function (Player $player, int $formData){});

                $form->setTitle(RandomUtils::colorMessage("&l&8WARP INFORMATION"));
                $form->setContent(RandomUtils::colorMessage($output));

                $form->setButton(RandomUtils::colorMessage("&8okay"));
                $form->send($sender);

                return true;
        }
}