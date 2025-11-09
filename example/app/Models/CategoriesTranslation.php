<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriesTranslation extends Model
{
    protected $table = 'categories_translations';

    protected $fillable = ['model_id', 'language_id', 'locale', 'name'];
}
