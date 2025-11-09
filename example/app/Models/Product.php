<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EbrahimHanna\TranslationManager\Traits\HasTranslations;

class Product extends Model
{
    use HasTranslations;

    protected $fillable = ['sku', 'price', 'stock', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $translation_model = [
        'model' => ProductTranslation::class,
        'owner_key' => 'product_id',
        'slug' => 'name',
    ];
}
