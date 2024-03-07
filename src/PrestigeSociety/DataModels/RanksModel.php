<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class RanksModel extends Model{
        protected $primaryKey = 'name';
        protected $table = 'ranks';
        protected $fillable = ['name', 'rank'];
}