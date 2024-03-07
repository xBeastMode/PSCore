<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class HomesModel extends Model{
        public $incrementing = false;
        protected $primaryKey = 'id';
        protected $table = 'homes';
        protected $fillable = ['name', 'x', 'y', 'z', 'level', 'owner'];
}