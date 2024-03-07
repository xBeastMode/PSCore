<?php
namespace PrestigeSociety\Teleport;
use pocketmine\player\Player;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\HomesModel;
class HomeAPI{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * TeleportAPI constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param        $player
         * @param string $name
         *
         * @return bool
         */
        public function homeExists($player, string $name){
                $record = HomesModel::query()->where([["name", "=", $name], ["owner", "=", RandomUtils::getName($player)]]);
                return $record->exists();
        }

        /**
         * @param        $player
         * @param string $name
         * @param int    $x
         * @param int    $y
         * @param int    $z
         * @param string $level
         */
        public function setHome($player, string $name, int $x, int $y, int $z, string $level){
                HomesModel::query()->updateOrCreate([["name", "=", $name], ["owner", "=", RandomUtils::getName($player)]], [
                    "name" => $name,
                    "owner" => RandomUtils::getName($player),
                    "x" => $x,
                    "y" => $y,
                    "z" => $z,
                    "level" => $level
                ]);
        }

        /**
         * @param        $player
         * @param string $name
         *
         * @return null|Position
         */
        public function getHomePosition($player, string $name): ?Position{
                $record = HomesModel::query()->where([["name", "=", $name], ["owner", "=", RandomUtils::getName($player)]]);

                if(!$record->exists()){
                        return null;
                }

                return RandomUtils::parsePosition([$record->value("x"), $record->value("y"), $record->value("z"), $record->value("level")]);
        }

        /**
         * @param $player
         *
         * @return string[]
         */
        public function getPlayerHomes($player): array{
                $record = HomesModel::query()->where("owner", "=", RandomUtils::getName($player));
                return $record->get()->toArray();
        }

        /**
         * @param        $player
         * @param string $name
         *
         * @return bool
         */
        public function deleteHome($player, string $name): bool{
                $record = HomesModel::query()->where([["name", "=", $name], ["owner", "=", RandomUtils::getName($player)]]);

                if(!$record->exists()){
                        return false;
                }

                $record->delete();
                return true;
        }

        /**
         * @param Player $player
         *
         * @return int
         */
        public function getTeleportDelay(Player $player): int{
                return $this->core->module_loader->teleport->getTeleportDelay($player, ["module" => "home", "permission" => Teleport::INSTANT_HOME_TELEPORT_PERMISSION]);
        }
}