<?php

namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\Player\SellForm;
use PrestigeSociety\Forms\FormManager;
class SellCommand extends CoreCommand{
        /** @var array */
        public static array $prices = [];
        /** @var int */
        protected int $SELL_ID = 0;

        /**
         * SellCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "sell", "Sell your items!", RandomUtils::colorMessage("&e/sell"), []);
                $this->core = $plugin;
                static::$prices = (new Config($plugin->getDataFolder() . "sell_prices.yml", Config::YAML, [
                    "1:0" => 3000
                ]))->getAll();

                $this->SELL_ID = FormManager::getNextFormId();
                $plugin->module_loader->form_manager->registerHandler($this->SELL_ID, SellForm::class);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testAll($sender)){
                        return false;
                }

                $this->core->module_loader->form_manager->sendForm($this->SELL_ID, $sender, static::$prices);
                return true;
        }
}
