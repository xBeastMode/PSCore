<?php
namespace PrestigeSociety\Directions;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\TextureUtils;
use PrestigeSociety\Directions\Entity\DirectionsEntity;
class DestinationTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var Player */
        protected Player $player;
        /** @var Vector3 */
        protected Vector3 $vector;
        
        /** @var DirectionsEntity|null */
        protected ?DirectionsEntity $entity_instance = null;

        /** @var string[] */
        protected array $fill;
        /** @var int */
        protected int $index = 0;

        /**
         * DestinationTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param Player              $player
         * @param Vector3             $vector3
         */
        public function __construct(PrestigeSocietyCore $core, Player $player, Vector3 $vector3){
                $this->core = $core;
                $this->player = $player;
                $this->vector = $vector3;

                $this->fill = array_fill(0, 5, "&l&e» &cFOLLOW ME &e«");
                $this->fill = array_merge($this->fill, array_fill(5, 5, "&l&8» &cFOLLOW ME &8«"));
        }

        /**
         * @return DirectionsEntity
         *
         * @throws \JsonException
         */
        protected function getEntityInstance(): DirectionsEntity{
                if($this->entity_instance === null){
                        $geometry = TextureUtils::getGeometryData(__DIR__ . "/arrow/arrow.json");
                        $texture = TextureUtils::getTexture(__DIR__ . "/arrow/arrow.png");

                        $nbt = RandomUtils::generateSkinCompoundTag($texture);

                        $entity = new DirectionsEntity($this->player->getLocation(), new Skin("Standard_Custom", $texture, "", "geometry.arrow", $geometry), $nbt);
                        $entity->setNameTag(RandomUtils::colorMessage("&l&8» &cFOLLOW ME &8«"));

                        $entity->setNameTagVisible();
                        $entity->setNameTagAlwaysVisible();

                        $entity->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_WIDTH, 0);
                        $entity->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 0);

                        $this->entity_instance = $entity;
                        $this->entity_instance->spawnTo($this->player);
                }
                return $this->entity_instance;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         *
         * @throws \JsonException
         */
        public function onRun(): void{
                $vector = $this->core->module_loader->directions->getSession($this->player);

                $stop = function (){
                        $this->getHandler()->cancel();
                        $this->core->module_loader->directions->stopDirections($this->player);

                        if(!$this->player->isClosed()){
                                RandomUtils::playSound("beacon.deactivate", $this->player, 1000, 1, true);
                                $position = $this->player->getWorld()->getSpawnLocation();

                                $pk = new SetSpawnPositionPacket();
                                $pk->spawnPosition = BlockPosition::fromVector3($position->asVector3());
                                $pk->causingBlockPosition = $pk->spawnPosition;
                                $pk->spawnType = SetSpawnPositionPacket::TYPE_WORLD_SPAWN;
                                $pk->dimension = DimensionIds::OVERWORLD;
                                $this->player->getNetworkSession()->sendDataPacket($pk);
                        }

                        $this->getEntityInstance()->close();
                };

                if($this->player->isClosed() || $vector === null || !$vector->equals($this->vector)){
                        $stop();
                        return;
                }

                $distance = round($this->player->getLocation()->distance($vector), 0);
                if($distance <= 3){
                        $msg = $this->core->getMessage("directions", "destination_reached");
                        $this->player->sendMessage(RandomUtils::colorMessage($msg));

                        $stop();
                        return;
                }

                $entity = $this->getEntityInstance();
                $direction_vector = $this->player->getDirectionVector();

                $direction_vector->x *= 4;
                $direction_vector->z *= 4;

                $direction_vector = $this->player->getLocation()->add($direction_vector->x, 2, $direction_vector->z);

                $entity->moveTo($direction_vector);
                $entity->lookAt($this->vector);

                $this->player->sendTip(RandomUtils::colorMessage("&l&8» &7you are &8$distance &7blocks away &8«"));
                $entity->setNameTag(RandomUtils::colorMessage("{$this->fill[$this->index]}\n&l&7we are &c$distance &7blocks away"));

                if(++$this->index > 9){
                        $this->index = 0;
                }
        }
}