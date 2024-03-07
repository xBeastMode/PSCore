<?php
namespace PrestigeSociety\Bosses\Task;
use pocketmine\entity\Location;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class BossSpawnTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var string */
        protected string $name;
        protected Location $location;
        protected int $size;
        protected int $damage;
        protected int $health;
        protected bool $custom;

        /**
         * BossSpawnTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param string              $name
         * @param Location            $location
         * @param int                 $size
         * @param int                 $damage
         * @param int                 $health
         * @param bool                $custom
         */
        public function __construct(PrestigeSocietyCore $core, string $name, Location $location, int $size, int $damage, int $health, bool $custom = false){
                $this->core = $core;
                $this->name = $name;
                $this->location = $location;
                $this->size = $size;
                $this->damage = $damage;
                $this->health = $health;
                $this->custom = $custom;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         *
         * @throws \JsonException
         */
        public function onRun(): void{
                if($this->core->module_loader->bosses->spawnBoss($this->name, $this->location, $this->size, $this->damage, $this->health, $this->custom)){
                        $msg = $this->core->getMessage("bosses", "respawn_message");
                        $msg = str_replace("@name", $this->name, $msg);
                        $this->core->getServer()->broadcastMessage(RandomUtils::colorMessage($msg));

                        foreach($this->core->getServer()->getOnlinePlayers() as $player){
                                RandomUtils::playSound("bass.reese", $player, 1000, 1, true);
                        }
                }
        }
}