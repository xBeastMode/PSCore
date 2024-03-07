<?php
namespace PrestigeSociety\Core\Task;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\ConsoleUtils;
class RunCommandsTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var int */
        protected int $seconds;
        /** @var array */
        protected array $commands;
        /** @var int */
        protected int $time = 0;

        /** @var callable */
        protected $cancel;

        /**
         * RunCommandsTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param int                 $seconds
         * @param array               $commands
         */
        public function __construct(PrestigeSocietyCore $core, int $seconds, array $commands){
                $this->core = $core;
                $this->seconds = $seconds;
                $this->commands = $commands;

                $this->cancel = function (){
                        $this->getHandler()->cancel();
                };
        }

        public function onRun(): void{
                foreach($this->commands as $command){
                        ConsoleUtils::dispatchCommandAsConsole(str_replace([
                            "@seconds",
                            "@rseconds"
                        ], [
                            $this->time,
                            $this->seconds - $this->time
                        ], $command));
                }

                if(++$this->time >= $this->seconds){
                        ($this->cancel)();
                }
        }
}