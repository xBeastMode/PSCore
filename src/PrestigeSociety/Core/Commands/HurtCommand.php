<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Task\HurtPlayerTask;
use PrestigeSociety\Core\Utils\RandomUtils;
class HurtCommand extends CoreCommand{
        /** @var TaskHandler[] */
        public static array $handlers = [];

        /**
         * HurtCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.hurt");
                parent::__construct($plugin, "hurt", "Hurt a certain player", RandomUtils::colorMessage("&eUsage: /hurt <start|stop> <player> [times] [tick-speed] [hearts]"), ["damage"]);
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

                if(count($args) < 2){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $action = strtolower($args[0]);

                if(!in_array($action, ["start", "stop"])){
                        $sender->sendMessage(TextFormat::RED . "Unknown action. It must be: 'start' or 'stop'");
                        return false;
                }

                $player = $sender->getServer()->getPlayerByPrefix($args[1]);
                if($player === null){
                        $sender->sendMessage(TextFormat::RED . "$args[1] is offline.");
                        return false;
                }

                $times = $args[2] ?? 15;
                $times = (int) $times;

                $tick = $args[3] ?? 20;
                $tick = (int) $tick;

                $hearts = $args[4] ?? 1;
                $hearts = (int) $hearts;

                if($tick < 1){
                        $sender->sendMessage(TextFormat::RED . "Tick speed must be greater than one (1).");
                        return false;
                }

                if($hearts < 1){
                        $sender->sendMessage(TextFormat::RED . "Hearts must be greater than one (1).");
                        return false;
                }

                switch($action){
                        case "start":
                                if(isset(self::$handlers[$player->getName()])){
                                        self::$handlers[$player->getName()]->cancel();
                                }

                                $h = $this->core->getScheduler()->scheduleRepeatingTask(new HurtPlayerTask($player, $times, $hearts), $tick);
                                self::$handlers[$player->getName()] = $h;
                                $this->core->sendMessage($sender, TextFormat::GREEN . "Started hurting {$player->getName()}, $times times with $hearts heart(s) of damage and a tick-speed of $tick.");
                                break;
                        case "stop":
                                if(!isset(self::$handlers[$player->getName()])){
                                        $this->core->sendMessage($sender, TextFormat::RED . "{$player->getName()} is not being hurt.");
                                        return false;
                                }

                                self::$handlers[$player->getName()]->cancel();
                                $this->core->sendMessage($sender, TextFormat::GREEN . "Stopped hurting {$player->getName()}.");

                                unset(self::$handlers[$player->getName()]);
                                break;
                }
                return true;
        }
}