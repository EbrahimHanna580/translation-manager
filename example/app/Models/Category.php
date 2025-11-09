<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EbrahimHanna\TranslationManager\Traits\HasTranslations;

class Category extends Model
{
    use HasTranslations;

    protected $table = 'categories';

    protected $fillable = ['name'];

    protected $translation_model = [
        'model' => CategoriesTranslation::class,
    ];
}
