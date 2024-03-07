<?php
namespace PrestigeSociety\Optimizer;
use pocketmine\entity\object\ItemEntity;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Optimizer\Utils\OptimizerInfo;
class Optimizer{
        public static function clearLag(): void{
                foreach(PrestigeSocietyCore::getInstance()->getServer()->getWorldManager()->getWorlds() as $level){
                        foreach($level->getEntities() as $entity){
                                if($entity instanceof ItemEntity){
                                        OptimizerInfo::saveClearedEntity($entity);
                                        $entity->close();
                                }
                        }
                }
                OptimizerInfo::addTimesCleared(1);
        }

        public static function emergencyRestoreEntities(): void{
                OptimizerInfo::restoreAllEntities();
        }
}