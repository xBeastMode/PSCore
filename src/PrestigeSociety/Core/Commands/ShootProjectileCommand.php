<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Entities\ItemProjectile;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Utils\RandomUtils;
class ShootProjectileCommand extends CoreCommand{
        /**
         * ShootProjectileCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.shootprojectile");
                parent::__construct($plugin, "shootprojectile", "Shoot a projectile", "Usage: /shootprojectile <item id> <item meta> <level> [eid] [force]", ["sproj"]);
        }

        /**
         * @param Entity $entity
         * @param Item   $item
         * @param int    $force
         *
         * @return ItemProjectile
         * @throws \JsonException
         */
        public function makeProjectile(Entity $entity, Item $item, int $force = 2): ItemProjectile{
                $skinData = str_repeat("\x00", 64 * 64 * 2);

                $dir = $entity->getDirectionVector();
                $dir->x *= $force;
                $dir->z *= $force;

                $nbt = RandomUtils::generateSkinCompoundTag($skinData);
                $nbt->setTag("HandItems", new ListTag([$item->nbtSerialize()]));

                $projectile = new ItemProjectile($entity->getLocation(), new Skin("Steve" . time(), $skinData, ), $nbt);

                $projectile->itemProjectile = $item;
                $projectile->shootingEntity = $entity;

                $projectile->setInvisible(true);

                return $projectile;
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         * @throws \JsonException
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }

                /** @var Player $sender */

                if(count($args) < 4 && !$this->testPlayerSilent($sender)){
                        $sender->sendMessage(TextFormat::RED . "Please specify entity id or run command in-game.");
                        $sender->sendMessage(TextFormat::YELLOW . $this->getUsage());

                        return false;
                }

                if(count($args) < 3 || !is_numeric($args[0]) || !is_numeric($args[1]) || (isset($args[3]) && !is_numeric($args[3])) || (isset($args[4]) && !is_numeric($args[4]))){
                        $sender->sendMessage(TextFormat::RED . "Please specify specify valid arguments.");
                        $sender->sendMessage(TextFormat::YELLOW . $this->getUsage());

                        return false;
                }

                $itemId = (int) $args[0];
                $itemMeta = (int) $args[1];

                $level = $args[2];

                $eid = (int) $eid = $args[3] ?? $sender->getId();
                $force = (int) $force = $args[4] ?? 2;

                $level = $sender->getServer()->getWorldManager()->getWorldByName($level) ?? $sender->getWorld()->getDisplayName();
                $entity = $level->getEntity($eid) ?? $sender;
                $item = ItemFactory::getInstance()->get($itemId, $itemMeta);

                if(!$entity instanceof Entity){
                        $this->core->sendMessage($sender, TextFormat::RED . "Please run command in-game.");
                        return false;
                }

                $this->makeProjectile($entity, $item, $force)->spawnToAll();
                $this->core->sendMessage($sender, TextFormat::GREEN . "Successfully spawned projectile.");

                return true;
        }
}