<?php
declare(strict_types = 1);
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\{
    CommandSender, defaults\VanillaCommand
};
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use PrestigeSociety\Core\PrestigeSocietyCore;
class PlaySoundXCommand extends VanillaCommand {
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * PlaySoundXCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                parent::__construct(
                    "playsoundx",
                    "Plays a sound",
                    "/playsoundx <sound> <player> [x] [y] [z] [volume] [pitch]"
                );
                $this->setPermission("command.playsoundx");
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param array         $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return true;
                }

                if(!isset($args[0]) || !isset($args[1])){
                        $sender->sendMessage("Usage: " . $this->usageMessage);

                        return false;
                }

                $server = Server::getInstance();
                /** @var Player $player */
                $player = $server->getPlayerByPrefix($args[1]);

                if(!($player instanceof Player)){
                        $this->core->sendMessage($sender, "Cannot find Player.");

                        return false;
                }

                $sound = $args[0] ?? "";
                $x = $args[2] ?? $player->getLocation()->getX();
                $y = $args[3] ?? $player->getLocation()->getY();
                $z = $args[4] ?? $player->getLocation()->getZ();
                $volume = $args[5] ?? 500;
                $pitch = $args[6] ?? 1;

                $pk = new PlaySoundPacket();
                $pk->soundName = $sound;
                $pk->x = $x;
                $pk->y = $y;
                $pk->z = $z;
                $pk->volume = $volume;
                $pk->pitch = $pitch;

                $player->getWorld()->broadcastPacketToViewers($player->getLocation(), $pk);
                $this->core->sendMessage($sender, "Playing " . $sound . " to " . $player->getName());

                return true;
        }
}
