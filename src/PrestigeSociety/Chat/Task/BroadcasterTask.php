<?php
namespace PrestigeSociety\Chat\Task;
use JetBrains\PhpStorm\Pure;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class BroadcasterTask extends Task{
        /** @var PrestigeSocietyCore */
        private PrestigeSocietyCore $core;
        /** @var int */
        private int $msgNumber = 0;
        /** @var string[] */
        private mixed $messages;

        /**
         * BroadcasterTask constructor.
         *
         * @param PrestigeSocietyCore $c
         */
        #[Pure] public function __construct(PrestigeSocietyCore $c){
                $this->core = $c;
                $this->messages = $this->core->getMessages()["broadcaster_messages"];
        }

        public function onRun(): void{
                $msg = RandomUtils::colorMessage($this->messages[$this->msgNumber]);
                $msg = RandomUtils::broadcasterTextReplacer($msg);

                if(++$this->msgNumber == count($this->messages)){
                        $this->msgNumber = 0;
                }

                $this->core->getServer()->broadcastMessage($msg);
        }
}