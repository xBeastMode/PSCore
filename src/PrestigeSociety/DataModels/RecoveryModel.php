<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class RecoveryModel extends Model{
        protected $primaryKey = 'id';
        protected $table = 'recovery';
        protected $fillable = ['name', 'item_id', 'item_data'];
}