<?php
namespace PrestigeSociety\Core\Utils;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\Block;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\Bed;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Potion;
use pocketmine\lang\Language;
use pocketmine\lang\Translatable;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Enchants\Enchants;
class RandomUtils{
        /** @var array */
        private static array $text_format = [];
        /** @var string[] */
        private static array $potion_names = [];
        /** @var string[] */
        private static array $effect_names = [];
        /** @var Language */
        private static Language $language;

        public static function init(){
                self::$language = new Language("eng");
        }

        /**
         * @param Position $position
         *
         * @return string
         */
        public static function positionToString(Position $position): string{
                return $position->x . ":" . $position->y . ":" . $position->z . ":" . $position->getWorld()->getDisplayName();
        }

        /**
         * @param Vector3 $vector
         *
         * @return string
         */
        public static function vectorToString(Vector3 $vector): string{
                return $vector->x . ":" . $vector->y . ":" . $vector->z;
        }

        /**
         * @param Position $position
         * @param int      $size
         *
         * @return AxisAlignedBB
         */
        public static function createAABB(Position $position, int $size): AxisAlignedBB{
                $minX = $position->x - $size;
                $minY = $position->y - $size;
                $minZ = $position->z - $size;
                $maxX = $position->x + $size;
                $maxY = $position->y + $size;
                $maxZ = $position->z + $size;

                return new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
        }

        /**
         * @param float $min
         * @param float $max
         *
         * @return float
         */
        public static function randomFloat (float $min, float $max): float{
                return ($min + lcg_value()*(abs($max - $min)));
        }

        /**
         * @param string $string
         *
         * @return bool
         */
        public static function checkBool(string $string): bool{
                $string = strtolower($string);
                return (in_array($string, array("true", "false", "1", "0", "yes", "no", true, false), true));
        }

        /**
         * @param $var
         *
         * @return bool
         */
        public static function toBool($var): bool{
                if (!is_string($var)) return (bool) $var;
                return match (strtolower($var)) {
                        "1", "true", "on", "yes", "y" => true,
                        default => false,
                };
        }

        /**
         * @param int $number
         *
         * @return string
         */
        public static function ordinal(int $number): string{
                $suffixes = [
                    "1" => "st",
                    "2" => "nd",
                    "3" => "rd",
                ];
                $string_value = (string) $number;
                $place = strlen($string_value) >= 2 ? $suffixes[substr($string_value, -1)] ?? "th" : $suffixes[$string_value] ?? "th";

                return $number . $place;
        }

        /**
         * @param string $string
         *
         * @return bool
         */
        public static function isJson(string $string): bool{
                $json = json_decode($string);
                return $json !== false && $json !== null && $string != $json;
        }

        /**
         * @param $player
         *
         * @return string
         */
        #[Pure] public static function getName($player): string{
                if($player instanceof Player){
                        return $player->getName();
                }
                return $player;
        }

        /**
         * @param string $json
         * @param bool   $assoc
         *
         * @return mixed
         *
         * @throws \Exception
         */
        public static function form_json_decode(string $json, bool $assoc = false): mixed{
                if(preg_match('/^\[(.+)\]$/s', $json, $matches) > 0){
                        $parts = preg_split('/(?:"(?:\\"|[^"])*"|)\K(,)/', $matches[1]); //Splits on commas not inside quotes, ignoring escaped quotes
                        foreach($parts as $k => $part){
                                $part = trim($part);
                                if($part === ""){
                                        $part = "\"\"";
                                }
                                $parts[$k] = $part;
                        }
                        $fixed = "[" . implode(",", $parts) . "]";
                        if(($ret = json_decode($fixed, $assoc)) === null){
                                throw new \Exception("Failed to fix JSON: " . json_last_error_msg() . "(original: $json, modified: $fixed)");
                        }
                        return $ret;
                }
                return json_decode($json, $assoc);
        }

        /**
         * @param string     $string
         * @param array|null $elements
         *
         * @return array|string
         */
        public static function textOptions(string $string, array $elements = null): array|string{
                $format = $string;
                if(isset(self::$text_format[$format])) $format = self::$text_format[$format];
                if(is_array($elements) && count($elements) >= 1){
                        $v = ["%%" => "%"];
                        $i = 0;
                        foreach($elements as $ret){
                                $v["%$i%"] = $ret;
                                ++$i;
                        }
                        $format = strtr($format, $v);
                }
                $format = str_replace("%n", "\n", $format);
                return str_replace("%%", "\xc2\xa7", $format);
        }

        /**
         * @param string $url
         *
         * @return string|bool
         */
        public static function getUrlContents(string $url): string|bool{
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                $data = curl_exec($curl);
                curl_close($curl);
                return $data;
        }

        /**
         * @param string $string
         *
         * @return array|string
         */
        public static function colorMessage(string $string): array|string{
                $string = preg_replace_callback("/@color_change/", function ($value){ return StringUtils::randomColor(); }, $string);

                $string = str_replace("@rand_color", StringUtils::randomColor(), $string);
                return str_replace("&", "\xc2\xa7", $string);
        }

        /**
         * @return Vector3
         */
        public static function randomVector(): Vector3{
                return new Vector3(mt_rand(0, 1000), mt_rand(0, 128), mt_rand(0, 1000));
        }

        /**
         * @param array $data
         *
         * @return null|Position
         */
        public static function parsePosition(array $data): ?Position{
                list($x, $y, $z, $level) = $data;
                $server = PrestigeSocietyCore::getInstance()->getServer();
                if(!$server->getWorldManager()->isWorldLoaded($level)){
                        $server->getWorldManager()->loadWorld($level, true);
                }
                $level = $server->getWorldManager()->getWorldByName($level);
                return $level === null ? null : new Position($x, $y, $z, $level);
        }

        /**
         * @param array $data
         *
         * @return null|Location
         */
        public static function parseLocation(array $data): ?Location{
                list($x, $y, $z, $level) = $data;
                $server = PrestigeSocietyCore::getInstance()->getServer();
                if(!$server->getWorldManager()->isWorldLoaded($level)){
                        $server->getWorldManager()->loadWorld($level, true);
                }
                $level = $server->getWorldManager()->getWorldByName($level);
                return $level === null ? null : new Location($x, $y, $z, $level, 0, 0);
        }

        /**
         * @param Vector3       $vector
         * @param AxisAlignedBB $bounds
         *
         * @return bool
         */
        public static function inBound(Vector3 $vector, AxisAlignedBB $bounds): bool{
                $minX = $bounds->minX;
                $minY = $bounds->minY;
                $minZ = $bounds->minZ;

                $maxX = $bounds->maxX;
                $maxY = $bounds->maxY;
                $maxZ = $bounds->maxZ;

                $check = $vector->x >= min($minX, $maxX) && $vector->x <= max($minX, $maxX);
                if($check){
                        $check = $vector->y >= min($minY, $maxY) && $vector->y <= max($minY, $maxY);
                        if($check){
                                $check = $vector->z >= min($minZ, $maxZ) && $vector->z <= max($minZ, $maxZ);
                                if($check){
                                        return true;
                                }
                        }
                }
                return false;
        }

        /**
         * @param Entity $target
         * @param float  $x
         * @param float  $z
         * @param float  $base
         */
        public static function knockBack(Entity $target, float $x, float $z, float $base = 0.4) : void{
                $f = sqrt($x * $x + $z * $z);
                if($f <= 0){
                        return;
                }
                $f = 1 / $f;

                $motion = clone $target->getMotion();

                $motion->x /= 2;
                $motion->y /= 2;
                $motion->z /= 2;
                $motion->x += $x * $f * $base;
                $motion->y += $base;
                $motion->z += $z * $f * $base;

                if($motion->y > $base){
                        $motion->y = $base;
                }

                $target->setMotion($motion);
        }

        /**
         * @param string $text
         * @param Player $player
         *
         * @return string
         */
        public static function authTextReplacer(string $text, Player $player): string{
                $on = count(PrestigeSocietyCore::getInstance()->getServer()->getOnlinePlayers());
                $max = PrestigeSocietyCore::getInstance()->getServer()->getMaxPlayers();
                $online_staff = [];
                foreach(PrestigeSocietyCore::getInstance()->getServer()->getOnlinePlayers() as $player){
                        if($player->getServer()->isOp($player->getName())) $online_staff[] = $player->getName();
                }
                if(!empty($online_staff)){
                        $online_staff = implode(", ", $online_staff);
                }else{
                        $online_staff = "";
                }
                return str_replace(["@online_players", "@max_players", "@prefix", "@online_staff_names", "@player"], [$on, $max, "Euphoria", $online_staff, $player->getName()], $text);
        }

        /**
         * @param string $text
         *
         * @return array|string
         */
        public static function broadcasterTextReplacer(string $text): array|string{
                $on = count(PrestigeSocietyCore::getInstance()->getServer()->getOnlinePlayers());
                $max = PrestigeSocietyCore::getInstance()->getServer()->getMaxPlayers();
                $onStaff = [];
                foreach(PrestigeSocietyCore::getInstance()->getServer()->getOnlinePlayers() as $player){
                        if($player->getServer()->isOp($player->getName())){
                                $onStaff[] = $player->getName();
                        }
                }
                if(!empty($onStaff)){
                        $onStaff = implode(", ", $onStaff);
                }else{
                        $onStaff = "";
                }
                $tps = PrestigeSocietyCore::getInstance()->getServer()->getTicksPerSecond();
                $load = PrestigeSocietyCore::getInstance()->getServer()->getTickUsage();
                return str_replace(["@online_players", "@max_players", "@prefix", "@online_staff_names", "@tps", "@server_load"],
                    [$on, $max, "Euphoria", $onStaff, $tps, $load], $text);
        }

        /**
         * @param string $text
         *
         * @return mixed
         */
        public static function restarterTextReplacer(string $text): string{
                $restarter = PrestigeSocietyCore::getInstance()->module_loader->restarter;

                $hours = $restarter->toHours();
                $minutes = $restarter->toMinutes();
                $seconds = $restarter->toSeconds();

                $text = str_replace(["@hours", "@minutes", "@seconds"], [$hours, $minutes, $seconds], $text);
                return $text;
        }

        /**
         * @param object $data
         *
         * @return array
         */
        public static function objectToArray(object $data): array{
                $array = [];
                if($data instanceof \stdClass or $data instanceof \ArrayObject){
                        foreach($data as $color => $hex){
                                $array[$color] = $hex;
                        }
                }
                return $array;
        }

        /**
         * @param string $soundName
         * @param        $to
         * @param int    $volume
         * @param float  $pitch
         * @param bool   $single
         */
        public static function playSound(string $soundName, $to, int $volume = 1000, float $pitch = 1, bool $single = false){
                if(!($to instanceof Player) && $to instanceof Position){
                        $pk = new PlaySoundPacket;
                        $pk->soundName = $soundName;
                        $pk->x = $to->x;
                        $pk->y = $to->y;
                        $pk->z = $to->z;
                        $pk->volume = $volume;
                        $pk->pitch = $pitch;

                        $to->getWorld()->broadcastPacketToViewers($to, $pk);
                }elseif($to instanceof Player){
                        $pk = new PlaySoundPacket;
                        $pk->soundName = $soundName;
                        $pk->x = $to->getLocation()->x;
                        $pk->y = $to->getLocation()->y;
                        $pk->z = $to->getLocation()->z;
                        $pk->volume = $volume;
                        $pk->pitch = $pitch;

                        if($single){
                                $to->getNetworkSession()->sendDataPacket($pk);
                        }else{
                                $to->getWorld()->broadcastPacketToViewers($to->getLocation(), $pk);
                        }
                }
        }

        /**
         * @param $items
         *
         * @return Item[]
         */
        public static function parseItemsWithEnchantments(array $items): array{
                $output_items = [];
                foreach($items as $key => $item){
                        if($item instanceof Item){
                                $output_items[] = $item;
                        }else{

                                $parts = explode(":", $item);

                                $id = (int) array_shift($parts);
                                $meta = (int) array_shift($parts);
                                $amount = array_shift($parts);
                                $name = array_shift($parts);
                                $lore = array_shift($parts);

                                $item = ItemFactory::getInstance()->get($id, $meta);

                                if(!($item->getId() === ItemIds::AIR)){
                                        if($lore && $lore !== ""){
                                                $item->setLore(explode("\n", RandomUtils::colorMessage($lore)));
                                        }

                                        $item->setCount($amount);
                                        $parts = implode(":", $parts);

                                        foreach(static::parseEnchantments([$parts]) as $enchant){
                                                $item->addEnchantment($enchant);
                                        }

                                        if(strtolower($name) !== "default"){
                                                $item->setCustomName(RandomUtils::colorMessage($name));
                                        }

                                        $output_items[] = $item;
                                }

                        }
                }

                return $output_items;
        }

        /**
         * @param array $enchantments
         *
         * @return EnchantmentInstance[]
         */
        public static function parseEnchantments(array $enchantments): array{
                /** @var EnchantmentInstance[] $output */
                $output = [];
                $index = 0;
                /** @var Enchantment $last_enchantment */
                $last_enchantment = null;

                foreach($enchantments as $enchantment){
                        if($enchantment instanceof EnchantmentInstance){
                                $output[] = $enchantment;
                        }else{
                                $parts = explode(":", $enchantment);

                                foreach($parts as $part){
                                        if((++$index % 2) === 0){
                                                if($last_enchantment !== null){
                                                        $balance = strtolower(substr($part, -1));
                                                        if($balance === "m"){
                                                                $level = substr($part, 0, -1);
                                                                $max = $last_enchantment->getMaxLevel();
                                                                $output[] = new EnchantmentInstance($last_enchantment, $level > $max ? $max : $level);
                                                        }else{
                                                                $output[] = new EnchantmentInstance($last_enchantment, (int)$part);
                                                        }
                                                }
                                        }else{
                                                $last_enchantment = self::smartParseEnchantment($part);
                                        }
                                }
                        }
                }

                return $output;
        }

        /**
         * @param mixed $anything
         *
         * @return Enchantment|null
         */
        public static function smartParseEnchantment(mixed $anything): ?Enchantment{
                $anything = strtolower($anything);
                $anything = Enchants::ENCHANTMENTS_ID_MAP[$anything] ?? Enchants::CUSTOM_ENCHANTMENTS_ID_MAP[$anything] ?? $anything;
                return EnchantmentIdMap::getInstance()->fromId((int) $anything);
        }

        /**
         * @param EnchantmentInstance|EffectInstance|Potion $type
         *
         * @return string
         */
        #[Pure] public static function getNameFromTranslatable(EnchantmentInstance|EffectInstance|Potion $type): string{
                $name = $type instanceof Potion ? $type->getType()->getDisplayName() : $type->getType()->getName();
                return $name instanceof Translatable ? self::$language->translate($name) : $name;
        }

        /**
         * @param $effects
         *
         * @return EffectInstance[]
         */
        public static function parseEffects($effects): array{
                $output = [];
                $effects = is_array($effects) ? $effects : [$effects];
                foreach($effects as $effect){
                        if($effect instanceof Effect){
                                $output[] = $effect;
                        }else{
                                $parts = explode(":", $effect);

                                $effect = StringToEffectParser::getInstance()->parse($parts[0]);
                                if($effect !== null){
                                        $output[] = new EffectInstance($effect, intval($parts[2] * 20), intval($parts[1]));
                                }
                        }
                }
                return $output;
        }

        /**
         * @param int $meta
         * 
         * @return string
         */
        public static function potionMetaToName(int $meta): string{
                try{
                        if(count(self::$potion_names) <= 0){
                                $parentConstants = (new \ReflectionClass(get_parent_class(Potion::class)))->getConstants();

                                $class = new \ReflectionClass(Potion::class);
                                foreach($class->getConstants() as $constant => $value){
                                        if(isset($parentConstants[$constant])) continue;

                                        $output = implode(" ", array_map(function ($value){ return ucfirst($value); }, explode("_", strtolower($constant))));
                                        self::$potion_names[$value] = $output;
                                }
                        }
                        return self::$potion_names[$meta] ?? "Unknown Potion";
                }catch(\ReflectionException $e){
                        return "Unknown Potion";
                }
        }

        /**
         * @param int $id
         *
         * @return string
         */
        public static function effectIdToName(int $id): string{
                try{
                        if(count(self::$effect_names) <= 0){
                                $class = new \ReflectionClass(Effect::class);
                                foreach($class->getConstants() as $constant => $value){
                                        $output = implode(" ", array_map(function ($value){ return ucfirst($value); }, explode("_", strtolower($constant))));
                                        self::$effect_names[$value] = $output;
                                }
                        }
                        return self::$effect_names[$id] ?? "Unknown Effect";
                }catch(\ReflectionException $e){
                        echo $e->getMessage() . "\n";

                        return "Unknown Potion";
                }
        }

        /**
         * @param string $skinData
         * @param string $name
         *
         * @return CompoundTag
         */
        public static function generateSkinCompoundTag(string $skinData, string $name = "Standard_Custom"): CompoundTag{
                $nbt = CompoundTag::create();
                $skinTag = CompoundTag::create();

                $skinTag->setString("Data", new StringTag($skinData));
                $skinTag->setString("Name", new StringTag("Standard_Custom"));

                $nbt->setTag("Skin", $skinTag);

                return $nbt;
        }

        /**
         * @param Player[] $target
         * @param Block[]  $blocks
         * @param int      $flags
         * @param bool     $optimizeRebuilds
         */
        public static function sendBlocks(array $target, array $blocks, int $flags = UpdateBlockPacket::FLAG_NONE, bool $optimizeRebuilds = false){
                /** @var ClientboundPacket $packets */
                $packets = [];
                if($optimizeRebuilds){
                        $chunks = [];
                        foreach($blocks as $block){
                                if($block === null){
                                        continue;
                                }

                                $pk = new UpdateBlockPacket();
                                $position = $block->getPosition();

                                $first = false;
                                if(!isset($chunks[$index = World::chunkHash($position->x >> 4, $position->z >> 4)])){
                                        $chunks[$index] = true;
                                        $first = true;
                                }

                                $pk->blockPosition = BlockPosition::fromVector3($block->getPosition());

                                if($block instanceof Block){
                                        $blockId = $block->getId();
                                        $blockData = $block->getMeta();
                                }else{
                                        $fullBlock = $position->getWorld()->getChunk($position->x >> 4, $position->y >> 4)->getFullBlock($position->x, $position->y, $position->z);
                                        $blockId = $fullBlock >> 4;
                                        $blockData = $fullBlock & 0xf;
                                }


                                $pk->blockRuntimeId = RuntimeBlockMapping::getInstance()->toRuntimeId(self::legacyToInternalStateId($blockId, $blockData));
                                $pk->flags = $first ? $flags : UpdateBlockPacket::FLAG_NONE;

                                $packets[] = $pk;
                        }
                }else{
                        foreach($blocks as $block){
                                if($block === null){
                                        continue;
                                }
                                $pk = new UpdateBlockPacket();
                                $position = $block->getPosition();

                                $pk->blockPosition = BlockPosition::fromVector3($block->getPosition());

                                if($block instanceof Block){
                                        $blockId = $block->getId();
                                        $blockData = $block->getMeta();
                                }else{
                                        $fullBlock = $position->getWorld()->getChunk($position->x >> 4, $position->y >> 4)->getFullBlock($position->x, $position->y, $position->z);
                                        $blockId = $fullBlock >> 4;
                                        $blockData = $fullBlock & 0xf;
                                }

                                $pk->blockRuntimeId = RuntimeBlockMapping::getInstance()->toRuntimeId(self::legacyToInternalStateId($blockId, $blockData));
                                $pk->flags = $flags;

                                $packets[] = $pk;
                        }
                }

                PrestigeSocietyCore::getInstance()->getServer()->broadcastPackets($target, $packets);
        }

        /**
         * @param int $legacyId
         * @param int $legacyMeta
         *
         * @return int
         */
        public static function legacyToInternalStateId(int $legacyId, int $legacyMeta): int{
                return ($legacyId << Block::INTERNAL_METADATA_BITS) | $legacyMeta;
        }

        /**
         * @return Permission
         */
        public static function getOperatorPermission(): Permission{
                return PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR);
        }
}