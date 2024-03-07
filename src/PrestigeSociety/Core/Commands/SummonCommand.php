<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
class SummonCommand extends CoreCommand{
        /**
         * SummonCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.summon");
                parent::__construct($plugin, "summon", "Summon an entity", "Usage: /summon <id> [x] [y] [z] [level]", []);
        }

        /**
         * @param int      $eid
         * @param Position $position
         */
        protected function summonEntity(int $eid, Position $position){
                $pk = new AddActorPacket();

                $pk->type = $eid;
                $pk->actorRuntimeId = Entity::nextRuntimeId();
                $pk->metadata = array();

                $pk->yaw = 0;
                $pk->pitch = 0;
                $pk->position = $position->asVector3();

                $position->getWorld()->broadcastPacketToViewers($position, $pk);
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

                if(count($args) < 5 && !$this->testPlayerSilent($sender)){
                        $sender->sendMessage(TextFormat::RED . "Please specify coordinates or run command in-game.");
                        $sender->sendMessage(TextFormat::YELLOW . $this->getUsage());

                        return false;
                }

                /** @var Player $sender */

                if(count($args) === 1 && is_numeric($args[0])){
                        $eid = (int) $args[0];
                        $this->summonEntity($eid, $sender->getPosition());

                        $this->core->sendMessage($sender, TextFormat::GREEN . "Successfully summoned entity.");
                        return true;
                }

                $testArgs = function () use ($args, $sender){
                        if(count($args) < 5 && !$this->testPlayerSilent($sender)) return false;
                        foreach($args as $index => $arg){
                                if($index === 4) return true;
                                if(!is_numeric($arg) && $arg !== "~") return false;
                                if($arg === "~" && !$this->testPlayer($sender)) return false;
                        }
                        return true;
                };

                if(count($args) < 4 || !$testArgs()){
                        $this->core->sendMessage($sender, TextFormat::RED . "Invalid arguments. " . TextFormat::YELLOW . $this->getUsage());
                        return false;
                }

                $level = count($args) >= 5 ? $sender->getServer()->getWorldManager()->getWorldByName($args[4]) : $sender->getWorld();
                if($level === null){
                        $this->core->sendMessage($sender, TextFormat::RED . "Invalid level. " . TextFormat::YELLOW . $this->getUsage());
                        return false;
                }

                $eid = (int) $args[0];
                $x = is_numeric($args[1]) ? (int) $args[1] : $sender->getLocation()->x;
                $y = is_numeric($args[2]) ? (int) $args[2] : $sender->getLocation()->y;
                $z = is_numeric($args[3]) ? (int) $args[3] : $sender->getLocation()->z;

                $this->summonEntity($eid, new Position($x, $y, $z, $level));
                $this->core->sendMessage($sender, TextFormat::GREEN . "Successfully summoned entity.");

                return true;
        }
}