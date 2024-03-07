<?php
namespace PrestigeSociety\Teleport\Commands\Teleport;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\Teleport;
class TpaCommand extends CoreCommand{
        /**
         * TpaCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "tpa", "Send teleport request to a player.", RandomUtils::colorMessage("&eUsage: /tpa <player>"), []);
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
                if(in_array($player->getWorld()->getDisplayName(), $this->core->module_loader->ranks->getAllRanks()) && $senderRank !== $playerRank){
                        $sender->sendMessage(RandomUtils::colorMessage($this->core->module_loader->teleport->getMessage("tpa_non_required_rank")));
                        return false;
                }

                $form = $this->core->module_loader->form_manager->getFastSimpleForm($player, function (Player $player, int $formData) use ($sender){
                        if($formData === 0){
                                $delay = $this->core->module_loader->teleport->getTeleportDelay($sender, ["module" => "teleport", "permission" => Teleport::INSTANT_TPA_TELEPORT_PERMISSION]);
                                $message = $this->core->module_loader->teleport->getTeleportMessage($sender, ["module" => "teleport", "vars" => ["@player" => $player->getName(), "@seconds" => $delay], "permission" => Teleport::INSTANT_TPA_TELEPORT_PERMISSION]);

                                $this->core->module_loader->teleport->teleport($sender, $player->getPosition(), $delay);
                                $sender->sendMessage(RandomUtils::colorMessage($message));
                        }else{
                                $sender->sendMessage(RandomUtils::colorMessage(str_replace("@player", $player->getName(), $this->core->module_loader->teleport->getMessage("teleport_denied"))));
                        }
                });

                $form->setTitle(RandomUtils::colorMessage("&l&8TELEPORT REQUEST"));
                $form->setContent(RandomUtils::colorMessage("&f{$sender->getName()} &7wants to teleport to you"));

                $form->setButton(RandomUtils::colorMessage("&l&8accept"));
                $form->setButton(RandomUtils::colorMessage("&l&8deny"));

                $form->send($player);

                $sender->sendMessage(RandomUtils::colorMessage(str_replace("@player", $player->getName(), $this->core->module_loader->teleport->getMessage("request_sent"))));

                return true;
        }
}