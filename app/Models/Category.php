<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    // Explicitly define the primary key and its type since it's 'char(5)'
    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Based on your migration, timestamps are excluded

    protected $fillable = [
        'category_id',
        'name',
        'quantity',
    ];

    /**
     * Define the relationship with the Product model.
     * A Category has many Products.
     */
    public function products(): HasMany
    {
        // The foreign key 'category_id' is on the Product model
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}