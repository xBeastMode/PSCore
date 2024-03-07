<?php
namespace PrestigeSociety\DataModels;
include_once __DIR__ . '/../../../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
class ShopModel extends Model{
        protected $table = 'shop';
        protected $fillable = ['id', 'item', 'price', 'amount', 'itemId', 'itemMeta', 'category'];
}