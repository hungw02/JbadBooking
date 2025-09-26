<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'import_price',
        'selling_price',
        'sale',
        'image',
        'quantity',
        'status',
        'type'
    ];

    public function importItems()
    {
        return $this->hasMany(ImportItem::class);
    }
    
    public function storages()
    {
        return $this->hasMany(Storage::class);
    }
}
