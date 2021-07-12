<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoryProduct;
use App\Models\Product;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public $timestamps = FALSE;

    /**
     * The categories that belong to the product.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, CategoryProduct::class);
    }
}
