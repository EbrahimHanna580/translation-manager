<?php

namespace EbrahimHanna\TranslationManager\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

trait HasTranslations
{
    protected static array $languageCache = [];

    public static function bootHasTranslations(): void
    {
        static::saved(function ($model) {
            self::manageTranslations($model);
        });

        static::deleted(function ($model) {
            $model->translationRelation()->delete();
        });
    }

    private static function manageTranslations($model): void
    {
        if (self::shouldSkipRoute($model)) {
            return;
        }

        $translations = request()->get(self::getConfig($model, 'translations_data_key'));

        if (!$translations || !is_array($translations)) {
            return;
        }

        foreach ($translations as $languageId => $fields) {
            if (self::isValidLanguage($languageId)) {
                self::manageRecord($model, $languageId, $fields);
            }
        }
    }

    private static function shouldSkipRoute($model): bool
    {
        $skipRoutes = $model->translation_model['skip_routes'] ?? null;

        if (!$skipRoutes) {
            return false;
        }

        return request()->routeIs($skipRoutes);
    }

    private static function manageRecord($model, int $languageId, array $translations): void
    {
        $foreignKey = self::getConfig($model, 'foreign_key');
        $ownerKey = self::getConfig($model, 'owner_key');
        $translationModel = $model->translation_model['model'];

        $translations = self::prepareTranslationData($model, $languageId, $translations);

        $translationModel::updateOrCreate(
            [
                $foreignKey => $languageId,
                $ownerKey => $model->id,
            ],
            $translations
        );
    }

    private static function prepareTranslationData($model, int $languageId, array $translations): array
    {
        if ($slugField = $model->translation_model['slug'] ?? null) {
            if (isset($translations[$slugField])) {
                $translations['slug'] = self::generateUniqueSlug(
                    $translations[$slugField],
                    $model->translation_model['model'],
                    $model->id
                );
            }
        }

        $localeColumn = self::getConfig($model, 'locale_column');
        if ($localeColumn) {
            $locale = self::getLanguageLocale($languageId, $model);
            if ($locale) {
                $translations[$localeColumn] = $locale;
            }
        }

        return $translations;
    }

    private static function getConfig($model, string $key): mixed
    {
        return $model->translation_model[$key] ?? config("laravel-translations.{$key}");
    }

    private static function isValidLanguage(int $languageId): bool
    {
        if (isset(self::$languageCache[$languageId])) {
            return self::$languageCache[$languageId];
        }

        $languageModel = config('laravel-translations.language_model');
        $exists = (new $languageModel)->exists($languageId);

        self::$languageCache[$languageId] = $exists;

        return $exists;
    }

    private static function getLanguageLocale(int $languageId, $model): ?string
    {
        $cacheKey = "locale_{$languageId}";

        if (isset(self::$languageCache[$cacheKey])) {
            return self::$languageCache[$cacheKey];
        }

        $languageCodeColumn = self::getConfig($model, 'language_code_column');
        $languageModel = config('laravel-translations.language_model');
        $language = (new $languageModel)->find($languageId);

        $locale = $language?->$languageCodeColumn;
        self::$languageCache[$cacheKey] = $locale;

        return $locale;
    }

    protected static function generateUniqueSlug(string $title, string $modelClass, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (self::slugExists($slug, $modelClass, $ignoreId)) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    protected static function slugExists(string $slug, string $modelClass, ?int $ignoreId = null): bool
    {
        return $modelClass::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }

    public function withTranslations(array $translations): static
    {
        foreach ($translations as $languageId => $translation) {
            self::manageRecord($this, $languageId, $translation);
        }

        return $this;
    }

    public function clearTranslations(int|string $languageId, ?string $key = null): bool
    {
        $foreignKey = $key ?? self::getConfig($this, 'foreign_key');

        return $this->translationRelation()
            ->where($foreignKey, $languageId)
            ->delete();
    }

    public function translationRelation(): HasMany
    {
        $translationModel = $this->translation_model['model'];
        $ownerKey = self::getConfig($this, 'owner_key');

        return $this->hasMany($translationModel, $ownerKey, 'id');
    }

    public function getTranslation(int|string $languageId, string $field, ?string $key = null): mixed
    {
        $foreignKey = $key ?? self::getConfig($this, 'foreign_key');

        return $this->translationRelation()
            ->where($foreignKey, $languageId)
            ->value($field);
    }

    public function getTranslations(int|string $languageId, string $field, ?string $key = null): mixed
    {
        return $this->getTranslation($languageId, $field, $key);
    }

    public function getAllTranslations(): HasMany
    {
        return $this->translationRelation();
    }

    public function getTranslationsByLanguage(int|string $languageId, ?string $key = null)
    {
        $foreignKey = $key ?? self::getConfig($this, 'foreign_key');

        return $this->translationRelation()
            ->where($foreignKey, $languageId)
            ->first();
    }
}
