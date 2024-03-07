<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class InventoriesModel extends Model{
        protected $primaryKey = 'id';
        protected $table = 'inventories';
        protected $fillable = ['name', 'inventory', 'world'];
}