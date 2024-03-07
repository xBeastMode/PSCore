<?php
namespace PrestigeSociety\Async;
use PrestigeSociety\Core\PrestigeSocietyCore;
class AsyncManager{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * AsyncManager constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param callable $callback
         * @param callable $returnCallback
         */
        public function submitAsyncCallback(callable $callback, callable $returnCallback){
                $this->core->getServer()->getAsyncPool()->submitTask(new AsyncCallbackTask($callback, $returnCallback));
        }

        /**
         * @param callable   $queryCallback
         * @param callable   $resultCallback
         * @param array|null $credentials
         */
        public function submitAsyncQuery(callable $queryCallback, callable $resultCallback, ?array $credentials = null){
                $this->core->getServer()->getAsyncPool()->submitTask(new AsyncQueryTask($queryCallback, $resultCallback, $credentials ?? $this->core->getConfig()->get("data_provider")));
        }
}