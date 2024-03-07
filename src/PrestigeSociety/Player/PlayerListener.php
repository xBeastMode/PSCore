<?php
namespace PrestigeSociety\Player;
use pocketmine\event\Listener;
use PrestigeSociety\Core\PrestigeSocietyCore;
class PlayerListener implements Listener{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * PlayerListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }
}