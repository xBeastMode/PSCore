<?php
namespace PrestigeSociety\Bosses\Task;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\scheduler\Task;
use PrestigeSociety\Bosses\Entity\BossTimer;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class BossRespawnTimer extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var string */
        protected string $name;
        protected Location $location;
        protected int $time;

        /** @var Human */
        protected Human $entity_instance;

        /**
         * BossRespawnTimer constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param string              $name
         * @param Location            $location
         * @param int                 $time
         *
         * @throws \JsonException
         */
        public function __construct(PrestigeSocietyCore $core, string $name, Location $location, int $time){
                $this->core = $core;
                $this->name = $name;
                $this->location = $location;
                $this->time = $time;

                $this->createEntityInstance();
        }

        /**
         * @throws \JsonException
         */
        protected function createEntityInstance(){
                $skinData = str_repeat("\x00", 64 * 64 * 2);

                $nbt = RandomUtils::generateSkinCompoundTag($skinData);
                $nbt->setByte("isBossTimer", 1);

                $entity = new BossTimer($this->location, new Skin("Steve" . time(), $skinData));

                $entity->setNameTagVisible(true);
                $entity->setScale(0.001);
                $entity->setImmobile(true);
                $entity->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::NO_AI, true);
                $entity->spawnToAll();

                if($entity instanceof BossTimer){
                        $entity->max_ticks = $this->time * 20;
                }

                $this->entity_instance = $entity;
        }

        /**
         * @return float
         */
        public function toHours(): float{
                return floor($this->time / 3600);
        }

        /**
         * @return float
         */
        public function toMinutes(): float{
                return floor(($this->time / 60) - (floor($this->time / 3600) * 60));
        }

        /**
         * @return float
         */
        public function toSeconds(): float{
                return floor($this->time % 60);
        }

        public function onCancel(): void{
                if($this->entity_instance !== null){
                        $this->entity_instance->flagForDespawn();
                }
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                if($this->time-- <= 0){
                        $this->getHandler()->cancel();
                        return;
                }

                $this->entity_instance->setNameTag(RandomUtils::colorMessage("&l&8» &aNext boss (&2{$this->name}&a) will respawn here.\n&aTime: &2{$this->toHours()}&a hours, &2{$this->toMinutes()}&a minutes and &2{$this->toSeconds()} &aseconds"));
        }
}