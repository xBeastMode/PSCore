<?php
namespace PrestigeSociety\Hats;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class HatCommand extends CoreCommand{
        /**
         * HatCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "hat", "Set a hat on your head!", RandomUtils::colorMessage("&eUsage: /hat [player]"), []);
                $this->setPermission("command.hat");
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testAll($sender)){
                        return false;
                }

                /** @var Player $sender */

                $player = $this->core->getServer()->getPlayerByPrefix($args[0] ?? '') ?? $sender;

                $item = $sender->getInventory()->getItemInHand();
                $mod = $this->core->module_loader->hats;

                if(!$mod->isWearable($item) && !$mod->hasHatEnabled($sender)){
                        $message = $this->core->getMessage("hats", "not_wearable");
                        $sender->sendMessage(RandomUtils::colorMessage($message));

                        return false;
                }

                $status = $mod->toggleHat($player, $item);
                $message = null;

                if($status === Hats::ENABLED){
                        $message = $this->core->getMessage("hats", "enabled");
                }elseif($status == Hats::DISABLED){
                        $message = $this->core->getMessage("hats", "disabled");
                }

                $message = str_replace(["@player", "@item"], [$player->getName(), $item->getName()], $message);
                $sender->sendMessage(RandomUtils::colorMessage($message));

                return true;
        }
}