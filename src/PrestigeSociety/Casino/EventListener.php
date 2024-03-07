<?php

namespace PrestigeSociety\Casino;
use pocketmine\event\Listener;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Events\Events\Casino\CasinoEndSpinEvent;
use PrestigeSociety\Player\Data\Settings;

class EventListener implements Listener{

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * EventListener constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param CasinoEndSpinEvent $event
         */
        public function onCasinoEndSpin(CasinoEndSpinEvent $event){
                $player = $event->getPlayer();

                if($event->isWon()){
                        $settings = $this->core->module_loader->player_data->getFreshPlayerSettings($player);

                        $timesWon = $settings->get(Settings::CASINO_WINS, 0);
                        $settings->set(Settings::CASINO_WINS, $timesWon + 1);

                        $settings->save();
                }
        }
}