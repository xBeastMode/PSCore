<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Task\RunCommandsTask;
use PrestigeSociety\Core\Utils\RandomUtils;
class RunCommandsCommand extends CoreCommand{
        /**
         * RunCommandsCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.runcmd");
                parent::__construct($plugin, "runcmd", "Schedule commands", RandomUtils::colorMessage("&eUsage: /runcmd <period> <delay> <times> <commands...>"), []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return false
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }

                if(count($args) < 4){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                if(!is_numeric($args[0]) || !is_numeric($args[1]) || !is_numeric($args[2])){
                        $sender->sendMessage(TextFormat::RED . "Please enter valid numbers.");
                        $sender->sendMessage(TextFormat::YELLOW . $this->getUsage());

                        return false;
                }

                $period = (int) array_shift($args);
                $delay = (int) array_shift($args);
                $times = (int) array_shift($args);

                $commands = explode("{cmd}", implode(" ", $args));

                $this->core->sendMessage($sender, TextFormat::GREEN . "Successfully scheduled task with period $period, delay $delay and will run $times times.");
                $this->core->getScheduler()->scheduleDelayedRepeatingTask(new RunCommandsTask($this->core, $times, $commands), $delay, $period);

                return false;
        }
}