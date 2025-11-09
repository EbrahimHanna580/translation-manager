<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EbrahimHanna\TranslationManager\Traits\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    protected $fillable = [
        'user_id',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $translation_model = [
        'model' => PostTranslation::class,
        'owner_key' => 'post_id',
        'slug' => 'title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
