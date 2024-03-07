<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class CreditsModel extends Model{
        protected $table = 'credits';
        protected $fillable = ['name', 'credits'];
}