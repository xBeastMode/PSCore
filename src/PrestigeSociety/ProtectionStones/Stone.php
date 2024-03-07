<?php
namespace PrestigeSociety\ProtectionStones;
use pocketmine\math\AxisAlignedBB;
use pocketmine\world\Position;
use PrestigeSociety\Core\Utils\RandomUtils;
class Stone implements \JsonSerializable{
        /** @var string */
        protected string $name;

        /** @var string */
        protected string $owner;
        /** @var Position|null */
        public ?Position $position = null;
        /** @var AxisAlignedBB|null */
        public ?AxisAlignedBB $bounds = null;
        /** @var array */
        protected array $helpers = [];

        /**
         * @param array $data
         *
         * @return null|Stone
         */
        public static function parse(array $data): ?Stone{
                list($minX, $minY, $minZ) = $data["min_bounds"];
                list($maxX, $maxY, $maxZ) = $data["max_bounds"];

                $name = $data["name"];
                $owner = $data["owner"];
                $position = RandomUtils::parsePosition($data["position"]);
                $bounds = new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
                $helpers = $data["helpers"];

                return $position === null ? null : new Stone($name, $owner, $position, $bounds, $helpers);
        }

        /**
         * Stone constructor.
         *
         * @param string        $name
         * @param string        $owner
         * @param Position      $position
         * @param AxisAlignedBB $bounds
         * @param array         $helpers
         */
        public function __construct(string $name, string $owner, Position $position, AxisAlignedBB $bounds, array $helpers){
                $this->name = $name;
                $this->owner = $owner;
                $this->position = $position;
                $this->bounds = $bounds;
                $this->helpers = $helpers;
        }

        /**
         * @return string
         */
        public function getName(): string{
                return $this->name;
        }

        /**
         * @param string $name
         */
        public function setName(string $name): void{
                $this->name = $name;
        }

        /**
         * @param string $player
         *
         * @return bool
         */
        public function isOwner(string $player): bool{
                return $this->owner === $player;
        }

        /**
         * @return string
         */
        public function getOwner(): string{
                return $this->owner;
        }

        /**
         * @param string $owner
         */
        public function setOwner(string $owner){
                $this->owner = $owner;
        }

        /**
         * @return Position
         */
        public function getPosition(): Position{
                return $this->position;
        }

        /**
         * @param Position $position
         */
        public function setPosition(Position $position){
                $this->position = $position;
        }

        /**
         * @return AxisAlignedBB
         */
        public function getBounds(): AxisAlignedBB{
                return $this->bounds;
        }

        /**
         * @param AxisAlignedBB $bounds
         */
        public function setBounds(AxisAlignedBB $bounds): void{
                $this->bounds = $bounds;
        }

        /**
         * @param string $helper
         *
         * @return bool
         */
        public function isHelper(string $helper): bool{
                return isset($this->helpers[$helper]);
        }

        /**
         * @return array
         */
        public function getHelpers(): array{
                return $this->helpers;
        }

        /**
         * @param string $helper
         * @param array  $settings
         */
        public function addHelper(string $helper, array $settings){
                $this->helpers[$helper] = $settings;
        }

        /**
         * @param string $helper
         */
        public function removeHelper(string $helper){
                if($this->isHelper($helper)){
                        unset($this->helpers[$helper]);
                }
        }

        /**
         * @param string $player
         *
         * @return bool
         */
        public function canPlace(string $player): bool{
                return $this->isHelper($player) ? $this->helpers[$player]["place"] : false;
        }

        /**
         * @param string $player
         *
         * @return bool
         */
        public function canBreak(string $player): bool{
                return $this->isHelper($player) ? $this->helpers[$player]["break"] : false;
        }

        /**
         * @param string $player
         *
         * @return bool
         */
        public function canInteract(string $player): bool{
                return $this->isHelper($player) ? $this->helpers[$player]["interact"] : false;
        }

        /**
         * Specify data which should be serialized to JSON
         *
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         *
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         *
         * @since 5.4.0
         */
        public function jsonSerialize(): mixed{
                return [
                    "min_bounds" => [
                        $this->bounds->minX,
                        $this->bounds->minY,
                        $this->bounds->minZ,
                    ],
                    "max_bounds" => [
                        $this->bounds->maxX,
                        $this->bounds->maxY,
                        $this->bounds->maxZ,
                    ],
                    "name" => $this->name,
                    "owner" => $this->owner,
                    "position" => [
                        $this->position->x,
                        $this->position->y,
                        $this->position->z,
                        $this->position->getWorld()->getDisplayName()
                    ],
                    "helpers" => $this->helpers
                ];
        }
}