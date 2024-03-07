<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Task\PlayerMotionTask;
use PrestigeSociety\Core\Utils\RandomUtils;
class MotionCommand extends CoreCommand{
        /** @var TaskHandler[] */
        protected array $handlers = [];

        /**
         * MotionCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.motion");
                parent::__construct($plugin, "motion", "Set your motion", RandomUtils::colorMessage("&eUsage: /motion <start|stop> <player> [speed-x] [speed-y] [speed-z]"), []);
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

                if($player->getName() !== $sender->getName() && !$sender->hasPermission("command.motion.others")){
                        $sender->sendMessage(TextFormat::RED . "You don't have permission to run this command on others.");
                        return false;
                }

                $speedX = $args[2] ?? 1;
                $speedX = (float) $speedX;

                $speedY = $args[3] ?? 1.5;
                $speedY = (float) $speedY;

                $speedZ = $args[4] ?? 1;
                $speedZ = (float) $speedZ;

                switch($action){
                        case "start":
                                if(isset($this->handlers[$player->getName()])){
                                        $this->handlers[$player->getName()]->cancel();
                                }

                                $h = $this->core->getScheduler()->scheduleRepeatingTask(new PlayerMotionTask($player, $speedX, $speedY, $speedZ), 1);
                                $this->handlers[$player->getName()] = $h;
                                $sender->sendMessage(TextFormat::GREEN . "Started motion of {$player->getName()} with a speed-x of $speedX, a speed-y of $speedY, and a speed-z of $speedZ");
                                break;
                        case "stop":
                                if(!isset($this->handlers[$player->getName()])){
                                        $sender->sendMessage(TextFormat::RED . "{$player->getName()} is not in motion.");
                                        return false;
                                }

                                $this->handlers[$player->getName()]->cancel();
                                $sender->sendMessage(TextFormat::GREEN . "Stopped {$player->getName()}'s motion'.");

                                unset($this->handlers[$player->getName()]);
                                break;
                }
                return true;
        }
}