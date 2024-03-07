<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\Chunk;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
class WildCommand extends CoreCommand{
        /**
         * WildCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "wild", "Go to a random location", "/wild", []);
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

                /** @var Player $sender */

                if(!in_array($sender->getWorld()->getDisplayName(), $this->core->getConfig()->get("wild_worlds"))){
                        $sender->sendTip(TextFormat::RED . "You cannot warp to wild in this world.");
                        return false;
                }

                $bounds = $this->core->getConfig()->get("wild_bounds");

                $spawnLocation = $sender->getWorld()->getSpawnLocation();

                $x = $spawnLocation->x + mt_rand(-$bounds, $bounds);
                $z = $spawnLocation->z + mt_rand(-$bounds, $bounds);

                if(!$sender->getWorld()->isChunkGenerated($x >> Chunk::COORD_BIT_SIZE, $z >> Chunk::COORD_BIT_SIZE)){
                        $sender->getWorld()->orderChunkPopulation($x >> Chunk::COORD_BIT_SIZE, $z >> Chunk::COORD_BIT_SIZE, null)
                            ->onCompletion(function () use ($sender, $x, $z, $spawnLocation){
                                $y = $spawnLocation->y + $sender->getWorld()->getHighestBlockAt($x, $z) + 1;

                                $sender->teleport($sender->getWorld()->getSafeSpawn(new Vector3($x, $y, $z)));
                                $sender->sendTip(TextFormat::GRAY . "Teleported to " . $sender->getLocation()->x . ", " . $sender->getLocation()->y . ", " . $sender->getLocation()->z);
                        }, function () use ($sender){
                                $sender->sendTip(TextFormat::RED . "Could not complete teleport: could not generate or populate chunk.");
                        });

                        return true;
                }

                $y = $spawnLocation->y + $sender->getWorld()->getHighestBlockAt($x, $z) + 1;

                $sender->teleport($sender->getWorld()->getSafeSpawn(new Vector3($x, $y, $z)));
                $sender->sendTip(TextFormat::GRAY . "Teleported to " . $sender->getLocation()->x . ", " . $sender->getLocation()->y . ", " . $sender->getLocation()->z);

                return true;
        }
}