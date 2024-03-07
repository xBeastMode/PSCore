<?php
namespace PrestigeSociety\Async;
use pocketmine\scheduler\AsyncTask;
class AsyncCallbackTask extends AsyncTask{
        /** @var callable */
        protected $callback;
        /** @var callable */
        protected $resultCallback;

        /**
         * AsyncQueryTask constructor.
         *
         * @param callable $callback
         * @param callable $resultCallback
         */
        public function __construct(callable $callback, callable $resultCallback){
                $this->callback = $callback;
                $this->resultCallback = $resultCallback;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                $this->setResult(($this->callback)());
        }

        public function onCompletion(): void{
                ($this->resultCallback)($this->getResult());
        }
}