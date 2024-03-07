<?php
namespace PrestigeSociety\Teleport;
use pocketmine\player\Player;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\WarpsModel;
class WarpAPI{
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
         * @param string      $name
         * @param string|null $owner
         *
         * @return bool
         */
        public function warpExists(string $name, string $owner = null): bool{
                $query = [["name", "=", $name]];
                if($owner !== null) $query[] = ["owner", "=", $owner];

                $record = WarpsModel::query()->where($query);
                return $record->exists();
        }

        /**
         * @param string      $name
         * @param int         $x
         * @param int         $y
         * @param int         $z
         * @param string      $level
         * @param string|null $owner
         */
        public function setWarp(string $name, int $x, int $y, int $z, string $level, string $owner = null){
                $query = [["name", "=", $name]];
                if($owner !== null) $query[] = ["owner", "=", $owner];

                WarpsModel::query()->updateOrCreate($query, [
                    "name" => $name,
                    "x" => $x,
                    "y" => $y,
                    "z" => $z,
                    "level" => $level,
                    "owner" => $owner,
                ]);
        }

        /**
         * @param string      $name
         * @param string|null $owner
         *
         * @return null|Position
         */
        public function getWarpPosition(string $name, string $owner = null): ?Position{
                $query = [["name", "=", $name]];
                if($owner !== null) $query[] = ["owner", "=", $owner];

                $record = WarpsModel::query()->where($query);

                if(!$record->exists()){
                        return null;
                }

                return RandomUtils::parsePosition([$record->value("x"), $record->value("y"), $record->value("z"), $record->value("level")]);
        }

        /**
         * @param string      $name
         * @param string|null $owner
         *
         * @return array
         */
        public function getWarpInfo(string $name, string $owner = null): array{
                $query = [["name", "=", $name]];
                if($owner !== null) $query[] = ["owner", "=", $owner];

                $record = WarpsModel::query()->where($query);

                if(!$record->exists()){
                        return [];
                }

                return $record->get()->toArray();
        }

        /**
         * @param string|null $owner
         *
         * @return string[]
         */
        public function getWarps(string $owner = null): array{
                if($owner !== null){
                        $record = WarpsModel::query()->where("owner", "=", $owner);
                        return $record->get()->toArray();
                }

                return WarpsModel::query()->get()->toArray();
        }

        /**
         * @param string      $name
         * @param string|null $owner
         *
         * @return bool
         */
        public function deleteWarp(string $name, string $owner = null): bool{
                $query = [["name", "=", $name]];
                if($owner !== null) $query[] = ["owner", "=", $owner];

                $record = WarpsModel::query()->where($query);

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
                return $this->core->module_loader->teleport->getTeleportDelay($player, ["module" => "warp", "permission" => Teleport::INSTANT_WARP_TELEPORT_PERMISSION]);
        }

        /**
         * @param             $player
         * @param string      $name
         *
         * @param string|null $owner
         * @return null|Position
         */
        public function getRelativeWarpPosition($player, string $name, string $owner = null): ?Position{
                $query = [["name", "=", $name]];
                if($owner !== null) $query[] = ["owner", "=", $owner];

                $record = WarpsModel::query()->where($query);
                if(!$record->exists()){
                        return null;
                }

                switch($record->value("level")){
                        case "@mine":
                                $rank = $this->core->module_loader->ranks->getRank($player);
                                $position = RandomUtils::parsePosition([0, 0, 0, $rank]);

                                if($position !== null){
                                        $position->x = $position->getWorld()->getSpawnLocation()->x;
                                        $position->y = $position->getWorld()->getSpawnLocation()->y;
                                        $position->z = $position->getWorld()->getSpawnLocation()->z;
                                }

                                return $position;
                                break;
                }
                return $this->getWarpPosition($name, $owner);
        }
}