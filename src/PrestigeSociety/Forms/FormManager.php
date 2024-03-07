<?php
namespace PrestigeSociety\Forms;
use Closure;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FastCustomForm;
use PrestigeSociety\Forms\FormHandler\FastSimpleForm;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\CustomForm;
use PrestigeSociety\UIForms\SimpleForm;
class FormManager{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var string[] */
        protected array $form_handlers = [];
        /** @var array */
        protected array $default_options = ["check_permissions" => false, "permission" => "command.form", "message" => "&l&4[!] &cYou may not open this form. Please go to the corresponding NPC."];

        /** @var int */
        protected static int $form_count = 0;

        /**
         * FormManager constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @return int
         */
        public static function getNextFormId(): int{
                return ++self::$form_count;
        }

        /**
         * @return int
         */
        public static function getRandomUniqueId(): int{
                return mt_rand(0, 2147483647);
        }

        /**
         * @return string[]
         */
        public function getHandlers(): array{
                return $this->form_handlers;
        }

        /**
         * @param int $id
         *
         * @return bool
         */
        public function handlerExists(int $id): bool{
                return isset($this->form_handlers[$id]);
        }

        /**
         * @param int    $id
         * @param string $class
         * @param bool   $force
         *
         * @return bool
         */
        public function registerHandler(int $id, string $class, bool $force = false): bool{
                if(!$this->handlerExists($id) || $force){
                        $this->form_handlers[$id] = $class;
                        PermissionManager::getInstance()->addPermission(new Permission($this->default_options["permission"] . ".$id", "Allows to open form with id $id"));
                        return true;
                }
                return false;
        }

        /**
         * @param int $id
         *
         * @return bool
         */
        public function unregisterHandler(int $id): bool{
                if($this->handlerExists($id)){
                        unset($this->form_handlers[$id]);
                        PermissionManager::getInstance()->removePermission($this->default_options["permission"] . ".$id");
                        return true;
                }
                return false;
        }

        /**
         * @param int $id
         *
         * @return null|string
         */
        public function getHandlerClass(int $id): ?string {
                return $this->handlerExists($id) ? $this->form_handlers[$id] : null;
        }

        /**
         * @param int $id
         *
         * @return null|FormHandler
         */
        public function getHandler(int $id): ?FormHandler {
                return $this->handlerExists($id) ? new $this->form_handlers[$id]($this->core, $id) : null;
        }

        /**
         * @param string $class
         *
         * @return string[]
         */
        public function filterFormHandlers(string $class): array{
                return array_filter($this->form_handlers, function (string $value) use ($class){ return stripos($value, $class) !== false; });
        }

        /**
         * @param string $class
         *
         * @return string[]
         */
        public function filterFormHandlerIds(string $class): array{
                return array_keys($this->filterFormHandlers($class));
        }

        /**
         * @param $string
         *
         * @return bool
         */
        public function formExists($string): bool{
                $handlers = $this->filterFormHandlerIds($string);
                if(count($handlers) > 0) return true;

                return isset($this->form_handlers[(int) $string]);
        }

        /**
         * @param int        $id
         * @param            $players
         * @param null       $extraData
         * @param array|null $options
         *
         * @return FormHandler|null
         */
        public function sendForm(int $id, $players, $extraData = null, ?array $options = null): ?FormHandler{
                $handler = $this->getHandler($id);

                if($options === null) $options = $this->default_options;
                if(!is_array($players)) $players = [$players];

                $check_permission = $options["check_permissions"] ?? $this->default_options["check_permissions"];
                $permission = $options["permission"] ?? $this->default_options["permission"] . ".$id";
                $message = $options["message"] ?? $this->default_options["message"];

                if($handler !== null){
                        foreach($players as $player){
                                /** @var Player $player */
                                if($check_permission && !$player->hasPermission($permission)){
                                        $player->sendMessage(RandomUtils::colorMessage($message));
                                        continue;
                                }
                                $handler->setData($extraData);
                                $handler->send($player);
                        }
                        return $handler;
                }
                return null;
        }

        /**
         * @param Player   $player
         * @param Closure $callback
         *
         * @return CustomForm
         */
        public function getFastCustomForm(Player $player, Closure $callback): CustomForm{
                $handler = new FastCustomForm($this->core, self::getNextFormId());
                $handler->send($player);

                /** @var CustomForm $form */
                $form = $handler->getData();
                $handler->setData($callback);

                return $form;
        }

        /**
         * @param Player   $player
         * @param Closure $callback
         *
         * @return SimpleForm
         */
        public function getFastSimpleForm(Player $player, Closure $callback): SimpleForm{
                $handler = new FastSimpleForm($this->core, self::getNextFormId());
                $handler->send($player);

                /** @var SimpleForm $form */
                $form = $handler->getData();
                $handler->setData($callback);

                return $form;
        }
}