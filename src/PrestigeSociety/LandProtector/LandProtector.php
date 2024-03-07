<?php
namespace PrestigeSociety\LandProtector;
use JetBrains\PhpStorm\Pure;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;
use PrestigeSociety\Core\PrestigeSocietyCore;
class LandProtector{
        const LAND_PROTECTOR_DIR = "lands/";
        const MODE_NO_DAMAGE = 0;
        const MODE_NO_EDIT = 1;
        const MODE_NO_TOUCH = 2;
        const MODE_NO_BURN = 3;
        const MODE_NO_EXPLODE = 4;

        /** @var array  */
        protected array $areas = [];
        /** @var array */
        protected array $areaNames = [];

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * LandProtector constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->core->getServer()->getPluginManager()->registerEvents(new LandProtectorListener($core), $this->core);
        }

        public function initFolder(){
                if(!file_exists($this->core->getDataFolder() . self::LAND_PROTECTOR_DIR)){
                        mkdir($this->core->getDataFolder() . self::LAND_PROTECTOR_DIR);
                }
                foreach($this->getAllAreasNames() as $datum){
                        $this->areas[$datum] = $this->getAreaData($datum);
                        $this->areaNames[] = $datum;
                }
                $this->core->module_loader->mine_resetter->reloadMines();
                $this->core->module_loader->portals->reloadPortals();
        }

        /**
         * @param string  $name
         * @param Vector3 $min
         * @param Vector3 $max
         * @param World   $level
         * @param array   $extraData
         *
         * @return bool
         */
        public function addArea(string $name, Vector3 $min, Vector3 $max, World $level, array $extraData = []): bool{
                if($this->areaExists($name)) return false;
                $values = [
                    "min" => [$min->x, $min->y, $min->z],
                    "max" => [$max->x, $max->y, $max->z],
                    "modes" => [
                        "damage" => false,
                        "edit" => false,
                        "touch" => false,
                        "burn" => false,
                        "explode" => false,
                    ],
                    "world" => $level->getDisplayName(),
                    "white_listed" => [],
                    "extra_data" => $extraData
                ];
                $this->saveAreaData($name . ".json", $values);
                $this->areas[$name] = $values;
                $this->areaNames[] = $name;
                return true;
        }

        /**
         * @param string $name
         * @param array  $data
         */
        public function updateAreaData(string $name, array $data){
                $this->areas[$name] = $data;
                $this->saveAreaData($name, $data);
        }

        /**
         * @param string $name
         *
         * @return bool
         */
        public function removeArea(string $name): bool{
                if($this->areaExists($name)){
                        $this->deleteAreaData($name);
                        return true;
                }
                return false;
        }

        /**
         * @param string $level
         *
         * @return bool
         */
        public function canDamageWold(string $level): bool{
                $levels = $this->core->module_configurations->land_protector;
                if(isset($levels[$level])){
                        return (bool)$levels[$level]["damage"];
                }
                return true;
        }

        /**
         * @param string $level
         *
         * @return bool
         */
        public function canEditWold(string $level): bool{
                $levels = $this->core->module_configurations->land_protector;
                if(isset($levels[$level])){
                        return (bool)$levels[$level]["edit"];
                }
                return true;
        }

        /**
         * @param string $level
         *
         * @return bool
         */
        public function canTouchWold(string $level): bool{
                $levels = $this->core->module_configurations->land_protector;
                if(isset($levels[$level])){
                        return (bool)$levels[$level]["touch"];
                }
                return true;
        }

        /**
         * @param string $level
         *
         * @return bool
         */
        public function canBurnWorld(string $level): bool{
                $levels = $this->core->module_configurations->land_protector;
                if(isset($levels[$level])){
                        return (bool)$levels[$level]["burn"];
                }
                return true;
        }

        /**
         * @param string $level
         *
         * @return bool
         */
        public function canExplodeWorld(string $level): bool{
                $levels = $this->core->module_configurations->land_protector;
                if(isset($levels[$level])){
                        return (bool)$levels[$level]["explode"];
                }
                return true;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canDamage(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if($data["modes"]["damage"]) continue;
                                return false;
                        }
                }
                return true;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canEdit(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if($data["modes"]["edit"]) continue;
                                return false;
                        }
                }
                return true;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canTouch(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if($data["modes"]["touch"]) continue;
                                return false;
                        }
                }
                return true;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canBurn(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if($data["modes"]["burn"]) continue;
                                return false;
                        }
                }
                return true;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canExplode(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if($data["modes"]["explode"]) continue;
                                return false;
                        }
                }
                return true;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canForceDamage(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if(isset($data["modes"]["force_damage"])){
                                        if(!$data["modes"]["force_damage"]) continue;
                                        return true;
                                }
                        }
                }
                return false;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canForceEdit(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if(isset($data["modes"]["force_edit"])){
                                        if(!$data["modes"]["force_edit"]) continue;
                                        return true;
                                }
                        }
                }
                return false;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canForceTouch(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if(isset($data["modes"]["force_touch"])){
                                        if(!$data["modes"]["force_touch"]) continue;
                                        return true;
                                }
                        }
                }
                return false;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canForceBurn(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if(isset($data["modes"]["force_burn"])){
                                        if(!$data["modes"]["force_burn"]) continue;
                                        return true;
                                }
                        }
                }
                return false;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function canForceExplode(Position $position): bool{
                foreach($this->areas as $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($position, $min, $max);

                        if($isVecInside and $position->getWorld()->getDisplayName() === $data["world"]){
                                if(isset($data["modes"]["force_explode"])){
                                        if(!$data["modes"]["force_explode"]) continue;
                                        return true;
                                }
                        }
                }
                return false;
        }

        /**
         * @param string $area
         * @param Player $player
         *
         * @return bool
         */
        public function addWhiteListed(string $area, Player $player): bool{
                if(!$this->isWhiteListed($area, $player) && ($data = $this->getAreaData($area)) !== null){
                        $data["white_listed"][$player->getName()] = true;
                        $this->updateAreaData($area, $data);

                        return true;
                }
                return false;
        }

        /**
         * @param string $area
         * @param Player $player
         * @return bool
         */
        public function removeWhiteListed(string $area, Player $player): bool{
                if($this->isWhiteListed($area, $player) && ($data = $this->getAreaData($area)) !== null){
                        unset($data["white_listed"][$player->getName()]);
                        $this->updateAreaData($area, $data);

                        return true;
                }
                return false;
        }

        /**
         * @param string $area
         * @param Player $player
         *
         * @return bool
         */
        public function isWhiteListed(string $area, Player $player): bool{
                if(($data = $this->getAreaData($area)) !== null){
                        return isset($data["white_listed"][$player->getName()]);
                }
                return false;
        }

        /**
         * @param string $area
         * @param int    $delay
         * @param string $title
         * @param string $subtitle
         *
         * @return bool
         */
        public function setRestricted(string $area, int $delay = 200, string $title = "", string $subtitle = ""): bool{
                return $this->setExtraData($area, [
                    "restricted_area" => [
                        "action_delay" => $delay,
                        "title" => $title,
                        "subtitle" => $subtitle
                    ]
                ]);
        }

        /**
         * @param string $area
         *
         * @return bool
         */
        public function removeRestricted(string $area): bool{
                return $this->removeExtraData($area, ["restricted_area" => []]);
        }

        /**
         * @param string $area
         * @param array  $data
         *
         * @return bool
         */
        public function addExtraData(string $area, array $data): bool{
                $added = false;
                $data2 = $this->getAreaData($area);
                if($data2 !== null){
                        foreach($data as $key => $datum){
                                if(!$this->areaHasExtraDataFromData($data2, $key)){
                                        $data2["extra_data"][$key] = $datum;
                                }
                        }
                        $this->saveAreaData($area, $data2);
                        $this->areas[$area] = $data2;
                        $added = true;
                }
                return $added;
        }

        /**
         * @param string $area
         * @param array  $data
         *
         * @return bool
         */
        public function setExtraData(string $area, array $data): bool{
                $added = false;
                $data2 = $this->getAreaData($area);
                if($data2 !== null){
                        foreach($data as $key => $datum){
                                $data2["extra_data"][$key] = $datum;
                        }
                        $this->saveAreaData($area, $data2);
                        $this->areas[$area] = $data2;
                        $added = true;
                }
                return $added;
        }

        /**
         * @param string $area
         * @param array  $data
         *
         * @return bool
         */
        public function removeExtraData(string $area, array $data): bool{
                $added = false;
                $data2 = $this->getAreaData($area);
                if($data2 !== null){
                        foreach($data as $key => $datum){
                                if($this->areaHasExtraDataFromData($data2, $key)){
                                        unset($data2["extra_data"][$key]);
                                }
                        }
                        $this->saveAreaData($area, $data2);
                        $this->areas[$area] = $data2;
                        $added = true;
                }
                return $added;
        }

        /**
         * @param string $area
         * @param string $data
         *
         * @return bool
         */
        public function areaHasExtraData(string $area, string $data): bool{
                if(($areaData = $this->getAreaData($area)) !== null){
                        return isset($areaData["extra_data"][$data]);
                }
                return false;
        }

        /**
         * @param Position $position
         *
         * @return bool
         */
        public function isInMine(Position $position): bool{
                $areas = $this->getAreasDataByVector($position, $position->world);
                foreach($areas as $data){
                        if($this->areaHasExtraDataFromData($data, "mine")){
                                return true;
                        }
                }
                return false;
        }

        /**
         * @param Position $position
         *
         * @return string
         */
        public function getMine(Position $position): ?string{
                $areas = $this->getAreasByVector($position, $position->world);
                foreach($areas as $area){
                        if($this->areaHasExtraData($area, "mine")){
                                return $area;
                        }
                }
                return null;
        }

        /**
         * @param array     $areaData
         * @param           $data
         *
         * @return bool
         */
        public function areaHasExtraDataFromData(array $areaData, $data): bool{
                if($areaData !== null){
                        return isset($areaData["extra_data"][$data]);
                }
                return false;
        }

        /**
         * @param $data
         *
         * @return array
         */
        public function getAreasWithData($data): array{
                $out = [];
                foreach($this->areaNames as $area){
                        if($this->areaHasExtraData($area, $data)){
                                $out[$area] = $this->getAreaData($area);
                        }
                }
                return $out;
        }

        /**
         * @return array
         */
        public function getAllAreasDirs(): array{
                $areas = [];
                foreach(glob($this->core->getDataFolder() . self::LAND_PROTECTOR_DIR . "*.json", GLOB_BRACE) as $name){
                        $areas[] = $name;
                }
                return $areas;

        }

        /**
         * @return array
         */
        public function getAllAreasNames(): array{
                $areas = [];
                foreach(glob($this->core->getDataFolder() . self::LAND_PROTECTOR_DIR . "*.json", GLOB_BRACE) as $name){
                        $name = str_replace("\\", "/", $name);
                        $name = explode("/", $name);
                        $name = end($name);
                        $name = str_replace(".json", "", $name);
                        $areas[] = $name;
                }
                return $areas;

        }

        /**
         * @param Vector3 $vector
         * @param World   $level
         *
         * @return array
         */
        #[Pure] public function getAreasByVector(Vector3 $vector, World $level): array{
                $areas = [];
                foreach($this->areas as $name => $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($vector, $min, $max);

                        if($isVecInside and $data["world"] === $level->getDisplayName()){
                                $areas[] = $name;
                        }
                }
                return $areas;
        }

        /**
         * @param Vector3 $vector
         * @param World   $level
         *
         * @return array
         */
        #[Pure] public function getAreasDataByVector(Vector3 $vector, World $level): array{
                $areas = [];
                foreach($this->areas as $name => $data){
                        if($data === null) continue;

                        list($min, $max) = $this->createVectors($data);
                        $isVecInside = $this->isVectorInside($vector, $min, $max);

                        if($isVecInside and $data["world"] === $level->getDisplayName()){
                                $areas[$name] = $data;
                        }
                }
                return $areas;
        }

        /**
         * @param array $data
         *
         * @return array
         */
        #[Pure] protected function createVectors(array $data): array{
                $min = new Vector3($data["min"][0], $data["min"][1], $data["min"][2]);
                $max = new Vector3($data["max"][0], $data["max"][1], $data["max"][2]);

                return [$min, $max];
        }

        /**
         * @param Vector3 $vector
         * @param Vector3 $min
         * @param Vector3 $max
         *
         * @return bool
         */
        public function isVectorInside(Vector3 $vector, Vector3 $min, Vector3 $max): bool{
                if($vector->x >= min($min->x, $max->x) && $vector->x <= max($min->x, $max->x)){
                        if($vector->y >= min($min->y, $max->y) && $vector->y <= max($min->y, $max->y)){
                                if($vector->z >= min($min->z, $max->z) && $vector->z <= max($min->z, $max->z)){
                                        return true;
                                }
                        }
                }
                return false;
        }

        /**
         * @param string     $name
         * @param bool|false $custom_name
         *
         * @return bool
         */
        #[Pure] public function areaExists(string $name, bool $custom_name = false): bool{
                if($custom_name){
                        return file_exists($name);
                }
                return file_exists($this->core->getDataFolder() . self::LAND_PROTECTOR_DIR . $name . (substr($name, -5) !== ".json" ? ".json" : ""));
        }

        /**
         * @param string $name
         * @param array  $values
         */
        public function saveAreaData(string $name, array $values): void{
                file_put_contents($this->core->getDataFolder() . self::LAND_PROTECTOR_DIR . $name . (substr($name, -5) !== ".json" ? ".json" : ""), json_encode($values, JSON_PRETTY_PRINT));
        }

        /**
         * @param string $name
         */
        public function deleteAreaData(string $name): void{
                unlink($this->core->getDataFolder() . self::LAND_PROTECTOR_DIR . $name . (substr($name, -5) !== ".json" ? ".json" : ""));
        }

        /**
         * @param string     $name
         * @param bool|false $custom
         *
         * @return mixed
         */
        public function getAreaData(string $name, bool $custom = false): mixed{
                if($custom){
                        if(!$this->areaExists($name, true)) return null;
                        return json_decode(file_get_contents($name), true);
                }

                if(isset($this->areas[$name])){
                        return $this->areas[$name];
                }

                if(!$this->areaExists($name . (substr($name, -5) !== ".json" ? ".json" : ""))) return null;
                return json_decode(file_get_contents($this->core->getDataFolder() . self::LAND_PROTECTOR_DIR . $name . (substr($name, -5) !== ".json" ? ".json" : "")), true);
        }

        /**
         * @param string $name
         * @param int    $mode_id
         *
         * @return bool
         */
        public function areaHasMode(string $name, int $mode_id): bool{
                switch($mode_id){
                        case self::MODE_NO_DAMAGE:
                                $data = $this->getAreaData($name);
                                if($data !== null){
                                        return $data["modes"]["damage"];
                                }
                                break;
                        case self::MODE_NO_EDIT:
                                $data = $this->getAreaData($name);
                                if($data !== null){
                                        return $data["modes"]["edit"];
                                }
                                break;
                        case self::MODE_NO_TOUCH:
                                $data = $this->getAreaData($name);
                                if($data !== null){
                                        return $data["modes"]["touch"];
                                }
                                break;
                }
                return false;
        }

        /**
         * @param string $name
         * @param int    $mode_id
         * @param bool   $value
         *
         * @return bool
         */
        public function setMode(string $name, int $mode_id, bool $value): bool{
                if($mode_id === null) return false;
                switch($mode_id){
                        case self::MODE_NO_DAMAGE:
                                $data = $this->getAreaData($name);
                                if($data !== null){
                                        $data["modes"]["damage"] = $value;
                                        $this->saveAreaData($name . ".json", (array)$data);
                                        $this->areas[$name] = $data;
                                        return true;
                                }
                                return false;
                        case self::MODE_NO_EDIT:
                                $data = $this->getAreaData($name);
                                if($data !== null){
                                        $data["modes"]["edit"] = $value;
                                        $this->saveAreaData($name . ".json", (array)$data);
                                        $this->areas[$name] = $data;
                                        return true;
                                }
                                return false;
                        case self::MODE_NO_TOUCH:
                                $data = $this->getAreaData($name);
                                if($data !== null){
                                        $data["modes"]["touch"] = $value;
                                        $this->saveAreaData($name . ".json", (array)$data);
                                        $this->areas[$name] = $data;
                                        return true;
                                }
                                return false;
                        case self::MODE_NO_BURN:
                                $data = $this->getAreaData($name);
                                if($data !== null){
                                        $data["modes"]["burn"] = $value;
                                        $this->saveAreaData($name . ".json", (array)$data);
                                        $this->areas[$name] = $data;
                                        return true;
                                }
                                return false;
                        case self::MODE_NO_EXPLODE:
                                $data = $this->getAreaData($name);
                                if($data !== null){
                                        $data["modes"]["explode"] = $value;
                                        $this->saveAreaData($name . ".json", (array)$data);
                                        $this->areas[$name] = $data;
                                        return true;
                                }
                                return false;
                }
                return false;
        }

        /**
         * @param $name
         *
         * @return int|null
         */
        public function getModeByName($name): ?int{
                switch(strtolower($name)){
                        case "damage":
                                return self::MODE_NO_DAMAGE;
                        case "edit":
                                return self::MODE_NO_EDIT;
                        case "touch":
                                return self::MODE_NO_TOUCH;
                        case "burn":
                                return self::MODE_NO_BURN;
                        case "explode":
                                return self::MODE_NO_EXPLODE;

                }
                return null;
        }

        /**
         * @return string[]
         */
        public function getAreaNames(): array{
                return $this->areaNames;
        }
}