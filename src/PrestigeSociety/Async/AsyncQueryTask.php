<?php
namespace PrestigeSociety\Async;
use Illuminate\Database\Capsule\Manager as Capsule;
use pocketmine\scheduler\AsyncTask;
class AsyncQueryTask extends AsyncTask{
        /** @var string */
        protected string $credentials;
        /** @var callable */
        protected $callback;
        /** @var callable */
        protected $resultCallback;

        /**
         * AsyncQueryTask constructor.
         *
         * @param callable $callback
         * @param callable $resultCallback
         * @param array    $credentials
         */
        public function __construct(callable $callback, callable $resultCallback, array $credentials){
                $this->credentials = serialize($credentials);
                $this->callback = $callback;
                $this->resultCallback = $resultCallback;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                include_once __DIR__ . '/../../../vendor/autoload.php';

                $capsule = new Capsule();

                $capsule->addConnection(unserialize($this->credentials));
                $capsule->setAsGlobal();
                $capsule->bootEloquent();

                $this->setResult(($this->callback)());
        }

        public function onCompletion(): void{
                ($this->resultCallback)($this->getResult());
        }
}