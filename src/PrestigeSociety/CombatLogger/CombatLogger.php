<?php
namespace PrestigeSociety\CombatLogger;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class CombatLogger{
        /** @var int[] */
        protected array $sessions = [];
        /** @var int[] */
        protected array $time = [];

        /** @var TaskHandler[] */
        protected array $tasks = [];

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * CombatLogger constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->core->getServer()->getPluginManager()->registerEvents(new CombatLoggerListener($this->core), $this->core);
        }

        /**
         * @param Player $player
         *
         * @return int
         */
        public function getTime(Player $player): int{
                return $this->inCombat($player) ? $this->time[spl_object_hash($player)] - (time() - $this->sessions[spl_object_hash($player)]) : 0;
        }

        /**
         * @param Player $player
         * @param int    $time
         *
         * @return bool
         */
        public function checkTime(Player $player, int $time = -1): bool{
                if($time === -1){
                        $time = $this->core->getConfig()->getAll()["combat_logger"]["time"];
                }

                $inCombat = function (Player $player, $time){

                        $this->endTask($player);
                        $this->startTask($time, $player);

                        $this->sessions[spl_object_hash($player)] = time();
                        $this->time[spl_object_hash($player)] = $time;
                };

                if(!isset($this->sessions[spl_object_hash($player)])){
                        $inCombat($player, $time);
                        $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("combat_logger", "pvp_danger")));
                        return false;
                }

                if((time() - $this->sessions[spl_object_hash($player)]) <= $time){
                        $inCombat($player, $time);
                        return true;
                }

                return false;
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function inCombat(Player $player): bool{
            return isset($this->sessions[spl_object_hash($player)]);
        }

        /**
         * @param Player $player
         *
         * @return bool
         */
        public function endTime(Player $player): bool{
                if(isset($this->sessions[spl_object_hash($player)])){
                        $this->endTask($player);

                        unset($this->sessions[spl_object_hash($player)]);
                        unset($this->time[spl_object_hash($player)]);

                        return true;
                }
                return false;
        }

        /**
         * @param $time
         *
         * @param Player $player
         */
        protected function startTask($time, Player $player): void{
                $handler = $this->core->getScheduler()->scheduleDelayedTask($task = new CombatLoggerTask($this->core, $player), 20 * $time);
                $task->setHandler($handler);

                $this->tasks[spl_object_hash($player)] = $handler;
        }

        /**
         * @param Player $player
         */
        protected function endTask(Player $player): void{
                if(isset($this->tasks[spl_object_hash($player)])){
                        $this->tasks[spl_object_hash($player)]->cancel();
                }
        }
}