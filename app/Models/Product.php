<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import for type hinting
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import for type hinting

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'name',
        'category_id', // NEW: Using the foreign key
        'price',
        'photo',
        'description',
        'is_available',
    ];

    /**
     * Define the relationship with the Category model.
     * A Product belongs to one Category.
     */
    public function category(): BelongsTo
    {
        // Explicitly defining foreign key ('category_id') and owner key ('category_id' in categories table)
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Define the relationship with the Order model.
     * A Product can be in many Orders.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'orderlist', 'product_id', 'order_id')
            ->withPivot('quantity', 'subtotal');    
    }
}