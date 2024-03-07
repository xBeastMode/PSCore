<?php
namespace PrestigeSociety\Credits;
use PrestigeSociety\Core\PrestigeSocietyCore;
class Credits{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * Credits constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public function playerExists($player): bool{
                return StaticCredits::playerExists($player);
        }

        /**
         * @param $player
         */
        public function addNewPlayer($player): void{
                $event = $this->core->module_loader->events->onCreditsRegisterPlayer($player);
                if(!$event->isCancelled()){
                        StaticCredits::addNewPlayer($player);
                }
        }

        /**
         * @param $player
         *
         * @param $credits
         */
        public function setCredits($player, $credits): void{
                $event = $this->core->module_loader->events->onSetCredits($player, $credits);

                if(!$event->isCancelled()){
                        StaticCredits::setCredits($player, $event->getCredits());
                }
        }

        /**
         * @param $player
         * @param $credits
         */
        public function addCredits($player, $credits): void{
                $event = $this->core->module_loader->events->onAddCredits($player, $credits);

                if(!$event->isCancelled()){
                        StaticCredits::addCredits($player, $event->getCredits());
                }
        }

        /**
         * @param $player
         * @param $credits
         *
         * @return bool
         */
        public function subtractCredits($player, $credits): bool{
                $event = $this->core->module_loader->events->onSubtractCredits($player, $credits);

                if(!$event->isCancelled()){
                        return StaticCredits::subtractCredits($player, $event->getCredits());
                }
                return false;
        }

        /**
         * @param $from
         * @param $to
         * @param $credits
         *
         * @return bool
         */
        public function payCredits($from, $to, $credits): bool{
                $event = $this->core->module_loader->events->onPayCredits($from, $to, $credits);

                if(!$event->isCancelled()){
                        return StaticCredits::payCredits($from, $to, $event->getCredits());
                }
                return false;
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getCredits($player): int{
                return StaticCredits::getCredits($player);
        }

}