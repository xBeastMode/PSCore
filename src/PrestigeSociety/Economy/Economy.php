<?php

namespace PrestigeSociety\Economy;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\Bank\BankForm;
use PrestigeSociety\Forms\FormList\Bank\BuyCreditForm;
use PrestigeSociety\Forms\FormList\Bank\DepositForm;
use PrestigeSociety\Forms\FormList\Bank\PayForm;
use PrestigeSociety\Forms\FormList\Bank\WithdrawForm;
use PrestigeSociety\Forms\FormManager;
class Economy{

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        public int $BUY_CREDIT_ID;
        public int $WITHDRAW_ID = 0;
        public int $PAY_ID = 0;
        public int $BANK_ID = 0;
        public int $DEPOSIT_ID = 0;

        /**
         * Economy constructor.
         * 
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->BANK_ID = FormManager::getNextFormId();
                $this->DEPOSIT_ID = FormManager::getNextFormId();
                $this->PAY_ID = FormManager::getNextFormId();
                $this->WITHDRAW_ID = FormManager::getNextFormId();
                $this->BUY_CREDIT_ID = FormManager::getNextFormId();

                $this->core->module_loader->form_manager->registerHandler($this->BANK_ID, BankForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->DEPOSIT_ID, DepositForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->PAY_ID, PayForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->WITHDRAW_ID, WithdrawForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->BUY_CREDIT_ID, BuyCreditForm::class);
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public function playerExists($player): bool{
                return StaticEconomy::playerExists($player);
        }

        /**
         * @param $player
         */
        public function addNewPlayer($player): void{
                $event = $this->core->module_loader->events->onEconomyRegisterPlayer($player);

                if(!$event->isCancelled()){
                        StaticEconomy::addNewPlayer($player);
                }
        }

        /**
         * @param $player
         *
         * @param $money
         */
        public function setMoney($player, $money): void{
                $event = $this->core->module_loader->events->onSetMoney($player, $money);

                if(!$event->isCancelled()){
                        StaticEconomy::setMoney($player, $money);
                }
        }

        /**
         * @param $player
         * @param $money
         */
        public function addMoney($player, $money): void{
                $event = $this->core->module_loader->events->onAddMoney($player, $money);

                if(!$event->isCancelled()){
                        StaticEconomy::addMoney($player, $money);
                }
        }

        /**
         * @param $player
         * @param $money
         *
         * @return bool
         */
        public function subtractMoney($player, $money): bool{
                $event = $this->core->module_loader->events->onSubtractMoney($player, $money);

                if(!$event->isCancelled()){
                        return StaticEconomy::subtractMoney($player, $money);
                }
                return false;
        }

        /**
         * @param $from
         * @param $to
         * @param $money
         *
         * @return bool
         */
        public function payMoney($from, $to, $money): bool{
                $event = $this->core->module_loader->events->onPayMoney($from, $to, $money);

                if(!$event->isCancelled()){
                        return StaticEconomy::payMoney($from, $to, $money);
                }
                return false;
        }

        /**
         * @param $player
         *
         * @return int
         */
        public function getMoney($player): int{
                return StaticEconomy::getMoney($player);
        }

        /**
         * @param int $amount
         *
         * @return int[][]
         */
        public function getTopMoney(int $amount): array{
                return StaticEconomy::getTopMoney($amount);
        }

        /**
         * @param Item $item
         *
         * @return bool
         */
        public function isCashItem(Item $item): bool{
                return $item->getNamedTag()->getLong("cash_amount", 0) !== 0;
        }

        /**
         * @param Player      $player
         * @param int         $amount
         * @param null|string $customName
         * @param bool        $autoCharge
         *
         * @return bool
         */
        public function withdraw(Player $player, int $amount, string $customName = null, bool $autoCharge = false): bool{
                $item = VanillaItems::PAPER();

                $cash = $this->getCash($player);
                $newAmount = $amount + $cash;

                if(($cash <= 0) && !$player->getInventory()->canAddItem($item)){
                        return false;
                }
                $this->removeCash($player);

                $item->setCustomName($customName ?? RandomUtils::colorMessage("&r&l&a$" . number_format($newAmount, 0, ".", ",") . " &9CASH\n&r&l&adeposit in &2/bank &a-> &2deposit"));
                $item->getNamedTag()->setLong("cash_amount", $newAmount);

                if($autoCharge){
                        $this->subtractMoney($player, $amount);
                }

                $player->getInventory()->addItem($item);
                return true;
        }

        /**
         * @param Player $player
         * @param int    $amount
         * @param bool   $autoDeposit
         *
         * @return bool
         */
        public function deposit(Player $player, int $amount, bool $autoDeposit = true): bool{
                $total = 0;
                $items = [];

                foreach($player->getInventory()->getContents() as $item){
                        if($item->getNamedTag()->getLong("cash_amount", false) != false){
                                $count = $item->getCount();
                                $money = $item->getNamedTag()->getLong("cash_amount") * $count;

                                if($money >= $amount){
                                        $money -= $amount;
                                        $player->getInventory()->remove($item);
                                        if($money > 0){
                                                $this->withdraw($player, $money);
                                        }
                                        if($autoDeposit){
                                                $this->addMoney($player, $amount);
                                        }
                                        return true;
                                }

                                $total += $money;
                                $items[] = $item;
                        }
                }

                if($total >= $amount){
                        $total -= $amount;
                        $player->getInventory()->removeItem(...$items);
                        if($total > 0){
                                $this->withdraw($player, $total);
                        }
                        if($autoDeposit){
                                $this->addMoney($player, $amount);
                        }

                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         *
         * @return int
         */
        public function getCash(Player $player): int{
                $total = 0;

                foreach($player->getInventory()->getContents() as $item){
                        if($item->getNamedTag()->getLong("cash_amount", false) != false){
                                $count = $item->getCount();
                                $total += $item->getNamedTag()->getLong("cash_amount") * $count;
                        }
                }

                return $total;
        }

        /**
         * @param Player $player
         */
        public function removeCash(Player $player): void{
                foreach($player->getInventory()->getContents() as $item){
                        if($item->getNamedTag()->getLong("cash_amount", false) != false){
                                $player->getInventory()->remove($item);
                        }
                }
        }
}