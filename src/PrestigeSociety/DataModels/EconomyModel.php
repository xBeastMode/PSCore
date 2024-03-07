<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class EconomyModel extends Model{
        protected $table = 'economy';
        protected $fillable = ['name', 'money'];
}