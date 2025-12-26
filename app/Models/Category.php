<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    /** Primary key configuration for char(5) type */
    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /** Mass Assignable Attributes */
    protected $fillable = [
        'category_id',
        'name',
        'quantity',
    ];

    /** Relationship: Products in this Category */
    public function products(): HasMany
    {
        // The foreign key 'category_id' is on the Product model
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}