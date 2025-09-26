<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Storage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_name',
        'quantity',
        'total_price',
        'transaction_type',
        'status',
        'note',
    ];

    protected $casts = [
        'status' => 'string', // 'returned', 'not_returned', 'completed'
        'transaction_type' => 'string' // 'sale', 'rent'
    ];

    public function product()
    {
        // If we have multiple products (comma-separated), just relate to the first one
        if (str_contains($this->product_id, ',')) {
            $firstProductId = explode(',', $this->product_id)[0];
            return $this->belongsTo(Product::class, 'product_id')->where('id', $firstProductId);
        }
        
        // Original single product relationship
        return $this->belongsTo(Product::class);
    }

    /**
     * Get all products associated with this storage transaction
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsAttribute()
    {
        if (!str_contains($this->product_id, ',')) {
            // Single product
            return Product::where('id', $this->product_id)->get();
        }
        
        // Multiple products
        $productIds = explode(',', $this->product_id);
        return Product::whereIn('id', $productIds)->get();
    }
}
