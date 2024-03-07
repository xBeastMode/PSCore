<?php
namespace PrestigeSociety\Optimizer\Utils;
use pocketmine\entity\Entity;
class OptimizerInfo{
        /** @var int */
        private static int $times_clears = 0;
        /** @var Entity[] */
        private static array $entities_cleared = [];

        /**
         * @return int
         */
        public static function getTimesCleared(): int{
                return self::$times_clears;
        }

        public static function resetTimesCleared(){
                self::$times_clears = 0;
        }

        /**
         * @param int $times
         */
        public static function addTimesCleared(int $times){
                self::$times_clears += $times;
        }

        /**
         * @param int $times
         */
        public static function subtractTimesCleared(int $times){
                self::$times_clears -= $times;
        }

        /**
         *
         * @param Entity $entity
         */
        public static function saveClearedEntity(Entity $entity){
                self::$entities_cleared[$entity->getId()] = $entity;
        }

        public static function restoreAllEntities(){
                foreach(self::$entities_cleared as $entity){
                        $entity->respawnToAll();
                }
        }

        /**
         * @return Entity[]
         */
        public static function getClearedEntities(): array{
                return self::$entities_cleared;
        }

        /**
         * @return int
         */
        public static function getClearedEntitiesCount(): int{
                return count(self::$entities_cleared);
        }
}