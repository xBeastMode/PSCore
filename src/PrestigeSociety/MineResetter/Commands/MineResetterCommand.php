<?php

namespace PrestigeSociety\MineResetter\Commands;
use pocketmine\command\CommandSender;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\TextureUtils;
use PrestigeSociety\MineResetter\Entity\ResetMineEntity;
use PrestigeSociety\MineResetter\MineResetterListener;
class MineResetterCommand extends CoreCommand{
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "mineresetter", "spawn or remove mine reset entity", "Usage: spawnmineresetter", ["smc"]);
                $this->setPermission("command.mineresetter");
        }

        public function execute(CommandSender $sender, string $commandLabel, array $args){
                if(!$this->testPermissionAndPlayer($sender, $this->getPermission())){
                        return false;
                }

                if(isset($args[0]) && strtolower($args[0]) === "remove"){
                        MineResetterListener::$sessions[spl_object_hash($sender)] = true;
                        $sender->sendMessage(RandomUtils::colorMessage("&aHit mine reset human to remove it."));
                        return true;
                }

                /** @var Player $sender */

                $geometry = TextureUtils::getGeometryData(__DIR__ . "/../arrow/arrow.json");
                $texture = TextureUtils::getTexture(__DIR__ . "/../arrow/arrow.png");

                $nbt = RandomUtils::generateSkinCompoundTag($texture);
                $mine = $sender->getLocation()->getWorld()->getDisplayName();

                $entity = new ResetMineEntity($sender->getLocation(), new Skin("Standard_Custom", $texture, "", "geometry.arrow", $geometry), $nbt);
                $entity->setNameTag(RandomUtils::colorMessage("&l&8» &aTAP TO RESET MINE &2$mine &8«"));

                $entity->setNameTagVisible();
                $entity->setNameTagAlwaysVisible();

                $entity->spawnToAll();
                return true;
        }
}