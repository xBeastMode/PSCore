<?php
namespace PrestigeSociety\Teleport\Commands\Teleport;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\Teleport;
class TpHereCommand extends CoreCommand{
        /**
         * TpHereCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "tphere", "Send request to a player to teleport to you.", RandomUtils::colorMessage("&eUsage: /tphere <player>"), []);
        }
        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPlayer($sender)){
                        return false;
                }

                if(count($args) <= 0){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $player = $args[0];
                if(($player = $sender->getServer()->getPlayerByPrefix($player)) === null){
                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@player", $args[0], $this->core->module_loader->teleport->getMessage("offline"))));
                        return false;
                }

                /** @var Player $sender */

                if($this->core->module_loader->combat_logger->inCombat($player)){
                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@player", $player->getName(), $this->core->module_loader->teleport->getMessage("request_failed"))));
                        return false;
                }

                $senderRank = $this->core->module_loader->ranks->getRank($sender);
                $playerRank = $this->core->module_loader->ranks->getRank($player);
                if(in_array($sender->getWorld()->getDisplayName(), $this->core->module_loader->ranks->getAllRanks()) && $senderRank !== $playerRank){
                        $sender->sendMessage(RandomUtils::colorMessage($this->core->module_loader->teleport->getMessage("non_required_rank")));
                        return false;
                }

                $form = $this->core->module_loader->form_manager->getFastSimpleForm($player, function (Player $player, int $formData) use ($sender){
                        if($formData === 0){
                                $delay = $this->core->module_loader->teleport->getTeleportDelay($player, ["module" => "teleport", "permission" => Teleport::INSTANT_TPA_TELEPORT_PERMISSION]);
                                $message = $this->core->module_loader->teleport->getTeleportMessage($player, ["module" => "teleport", "vars" => ["@player" => $sender->getName(), "@seconds" => $delay], "permission" => Teleport::INSTANT_TPA_TELEPORT_PERMISSION]);

                                $this->core->module_loader->teleport->teleport($player, $sender->getPosition(), $delay);
                                $player->sendMessage(RandomUtils::colorMessage($message));
                        }else{
                                $sender->sendMessage(RandomUtils::colorMessage(str_replace("@player", $player->getName(), $this->core->module_loader->teleport->getMessage("teleport_denied"))));
                        }
                });

                $form->setTitle(RandomUtils::colorMessage("&l&8TELEPORT REQUEST"));
                $form->setContent(RandomUtils::colorMessage("&f{$sender->getName()} &7wants you to teleport to them"));

                $form->setButton(RandomUtils::colorMessage("&l&8accept"));
                $form->setButton(RandomUtils::colorMessage("&l&8deny"));

                $form->send($player);

                $sender->sendMessage(RandomUtils::colorMessage(str_replace("@player", $player->getName(), $this->core->module_loader->teleport->getMessage("request_sent"))));

                return true;
        }
}