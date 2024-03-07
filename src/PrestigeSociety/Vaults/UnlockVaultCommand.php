<?php
namespace PrestigeSociety\Vaults;
use _64FF00\PurePerms\PurePerms;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class UnlockVaultCommand extends CoreCommand{
        public const MAX_CHECK = 1000;

        /**
         * UnlockVaultCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.unlockvault");
                parent::__construct($plugin, "unlockvault", "Unlock a vault for someone!", RandomUtils::colorMessage("&eUsage: /unlockvault <player> [count]"), ["unlockpv"]);
        }

        /**
         * @param CommandSender $sender
         * @param string $commandLabel
         * @param string[] $args
         *
         * @return void
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args){
                if(!$this->testPermission($sender)){
                        return;
                }
                if(count($args) <= 0){
                        $sender->sendMessage($this->getUsage());
                        return;
                }

                /** @var PurePerms $pp */
                $pp = $sender->getServer()->getPluginManager()->getPlugin("PurePerms");
                $player = $sender->getServer()->getPlayerByPrefix($args[0]);

                if($player === null){
                        $sender->sendMessage(TextFormat::RED . "$args[0] is offline.");
                        return;
                }

                if($player->hasPermission("pv.infinite")){
                        $messagePlayer = $this->core->getMessage("vaults", "max_vaults");

                        $player->sendMessage(RandomUtils::colorMessage($messagePlayer));
                        $sender->sendMessage(TextFormat::RED . "{$player->getName()} cannot unlocked anymore vaults.");

                        return;
                }

                $count = $args[1] ?? 1;
                $count = (int) $count;

                $numberUnlocked = [];

                for($i = 0; $i < $count; $i++){
                        for($j = 1; $j < self::MAX_CHECK; $j++){
                                if(!$player->hasPermission("pv.$j")){
                                        $numberUnlocked[] = $j;
                                        $pp->getUserDataMgr()->setPermission($player, "pv.$j");
                                        continue 2;
                                }
                        }
                }

                if(count($numberUnlocked) <= 0){
                        $messagePlayer = $this->core->getMessage("vaults", "max_vaults");

                        $player->sendMessage(RandomUtils::colorMessage($messagePlayer));
                        $sender->sendMessage(TextFormat::RED . "{$player->getName()} cannot unlocked anymore vaults.");

                        return;
                }

                $numberUnlocked = implode(", ", $numberUnlocked);

                $messagePlayer = $this->core->getMessage("vaults", "vault_unlocked");
                $messagePlayer = str_replace("@number", $numberUnlocked, $messagePlayer);

                $player->sendMessage(RandomUtils::colorMessage($messagePlayer));
                $sender->sendMessage(TextFormat::GREEN . "Unlocked vault #$numberUnlocked for {$player->getName()}");
        }

        /**
         * @return Plugin
         */
        public function getPlugin(): Plugin{
                return $this->core;
        }
}