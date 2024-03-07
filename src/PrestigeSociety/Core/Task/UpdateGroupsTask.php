<?php
namespace PrestigeSociety\Core\Task;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class UpdateGroupsTask extends Task{

        /** @var PrestigeSocietyCore */
        private PrestigeSocietyCore $core;

        /**
         * UpdateGroupsTask constructor.
         *
         * @param PrestigeSocietyCore $owner
         */
        public function __construct(PrestigeSocietyCore $owner){
                $this->core = $owner;
        }

        public function onRun(): void{
                $this->core->reloadGroupsConfig();
                $this->core->pruneGroupsConfig();

                $this->core->getLogger()->info(RandomUtils::colorMessage("&aUpdated and pruned all groups!"));
        }
}