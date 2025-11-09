# Laravel Translation Manager

[![Current version](https://img.shields.io/packagist/v/ebrahimhanna/translation-manager.svg?logo=composer)](https://packagist.org/packages/ebrahimhanna/translation-manager)
[![Monthly Downloads](https://img.shields.io/packagist/dm/ebrahimhanna/translation-manager.svg)](https://packagist.org/packages/ebrahimhanna/translation-manager/stats)
[![Total Downloads](https://img.shields.io/packagist/dt/ebrahimhanna/translation-manager.svg)](https://packagist.org/packages/ebrahimhanna/translation-manager/stats)
[![License](https://img.shields.io/packagist/l/ebrahimhanna/translation-manager.svg)](https://packagist.org/packages/ebrahimhanna/translation-manager)
[![PHP Version](https://img.shields.io/packagist/php-v/ebrahimhanna/translation-manager.svg)](https://packagist.org/packages/ebrahimhanna/translation-manager)

## Overview

Laravel Translation Management is a powerful, database-driven translation package that simplifies multi-language content management in Laravel applications. Store translations in dedicated database tables with automatic management through Eloquent events.

## Features

- **Database-Driven Translations**: Store translations in dedicated tables for dynamic management
- **Multi-Language Support**: Handle unlimited languages with ease
- **Automatic Translation Management**: Translations are saved/updated automatically via Eloquent events
- **Configurable Architecture**: Customize foreign keys, owner keys, and column names per model
- **Automatic Slug Generation**: Generate unique slugs from translatable fields
- **Locale Support**: Automatically store language codes in translation records
- **Route Skipping**: Selectively disable auto-save on specific routes
- **Performance Optimized**: Built-in caching for language lookups
- **Simple Integration**: Just add a trait to your models
- **Zero Dependencies**: Only requires Laravel/Eloquent


## Installation

Install the package via Composer:

```bash
composer require ebrahimhanna/translation-manager
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="EbrahimHanna\TranslationManager\PackageServiceProvider"
```

This will create `config/laravel-translations.php` with default settings.

## Quick Start

### 1. Create Database Tables

You need three types of tables:

**Languages table:**
```php
Schema::create('languages', function (Blueprint $table) {
    $table->id();
    $table->string('title'); // e.g., "English"
    $table->string('code');  // e.g., "en"
    $table->timestamps();
});
```

**Main model table (e.g., products):**
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('sku')->unique();
    $table->decimal('price', 10, 2);
    // ... other non-translatable fields
    $table->timestamps();
});
```

**Translation table (e.g., products_translations):**
```php
Schema::create('products_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
    $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
    $table->string('locale')->nullable(); // Auto-filled
    $table->string('name');               // Translatable field
    $table->string('slug')->nullable();   // Auto-generated
    $table->text('description')->nullable();
    $table->timestamps();

    $table->unique(['language_id', 'product_id']);
});
```

### 2. Set Up Your Models

**Main Model (Product.php):**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EbrahimHanna\TranslationManager\Traits\HasTranslations;

class Product extends Model
{
    use HasTranslations;

    protected $fillable = ['sku', 'price', 'stock'];

    protected $translation_model = [
        'model' => ProductTranslation::class,
        'owner_key' => 'product_id',
        'slug' => 'name', // Auto-generate slug from 'name' field
    ];
}
```

**Translation Model (ProductTranslation.php):**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $fillable = [
        'product_id',
        'language_id',
        'locale',
        'name',
        'slug',
        'description',
    ];
}
```

### 3. Use in Your Application

**Store with translations:**
```php
// The trait automatically saves translations from the request
Product::create([
    'sku' => 'PROD-001',
    'price' => 99.99,
]);

// Request should contain:
// translations[1][name] = "English Product Name"
// translations[1][description] = "English description"
// translations[2][name] = "اسم المنتج بالعربية"
// translations[2][description] = "وصف باللغة العربية"
```

**Retrieve translations:**
```php
$product = Product::find(1);

// Get specific field translation
$englishName = $product->getTranslation(1, 'name');
// or by language code
$arabicName = $product->getTranslation('ar', 'name', 'code');

// Get all translations for a language
$translation = $product->getTranslationsByLanguage(1);
echo $translation->name;
echo $translation->description;
echo $translation->slug; // auto-generated

// Get all translations
$allTranslations = $product->getAllTranslations;
```

## Advanced Usage

### Configuration Options

The `$translation_model` property accepts the following options:

```php
protected $translation_model = [
    // Required
    'model' => ProductTranslation::class,

    // Optional - Override global config
    'foreign_key' => 'language_id',              // FK to languages table
    'owner_key' => 'product_id',                 // FK to parent model
    'translations_data_key' => 'translations',   // Request array key
    'locale_column' => 'locale',                 // Column for language code
    'language_code_column' => 'code',            // Languages table code column

    // Optional - Advanced features
    'slug' => 'name',                            // Auto-generate slug from field
    'skip_routes' => ['products.update'],        // Skip auto-save on routes
];
```

### Global Configuration

Edit `config/laravel-translations.php`:

```php
return [
    'language_model' => 'App\Models\Language',
    'foreign_key' => 'language_id',
    'owner_key' => 'model_id',
    'translations_data_key' => 'translations',
    'language_code_column' => 'code',
    'locale_column' => 'locale', // Set to null to disable
];
```

### Form Structure

Create forms with nested arrays using language IDs as keys:

```blade
<form action="{{ route('products.store') }}" method="POST">
    @csrf

    <!-- Non-translatable fields -->
    <input type="text" name="sku" required>
    <input type="number" name="price" step="0.01" required>

    <!-- Translatable fields for each language -->
    @foreach($languages as $language)
        <div class="language-section">
            <h4>{{ $language->title }} ({{ $language->code }})</h4>

            <input type="text"
                   name="translations[{{ $language->id }}][name]"
                   placeholder="Product Name">

            <textarea name="translations[{{ $language->id }}][description]"
                      placeholder="Description"></textarea>
        </div>
    @endforeach

    <button type="submit">Create Product</button>
</form>
```

### Manual Translation Management

Use `withTranslations()` for batch operations or when routes are skipped:

```php
// Create product and manually add translations
$product = Product::create([
    'sku' => 'PROD-001',
    'price' => 99.99,
]);

$product->withTranslations([
    1 => ['name' => 'English Name', 'description' => 'English description'],
    2 => ['name' => 'اسم عربي', 'description' => 'وصف عربي'],
]);

// Method chaining
Product::create($data)->withTranslations($translations);
```

### Deleting Translations

```php
// Delete all translations for a specific language
$product->clearTranslations(1); // By language ID
$product->clearTranslations('en', 'code'); // By language code

// Deleting the model cascades to translations
$product->delete(); // All translations are auto-deleted
```

### API Methods

```php
// Get single field translation
$name = $product->getTranslation(1, 'name');
$name = $product->getTranslation('en', 'name', 'code');

// Get all fields for a language (returns model or null)
$translation = $product->getTranslationsByLanguage(1);
$translation = $product->getTranslationsByLanguage('en', 'code');

// Get all translations (returns HasMany relationship)
$allTranslations = $product->getAllTranslations;

// Access via Eloquent relationship
$product->translationRelation()->where('locale', 'en')->get();
```

## Use Cases

### E-commerce Products
```php
protected $translation_model = [
    'model' => ProductTranslation::class,
    'owner_key' => 'product_id',
    'slug' => 'name',
    'locale_column' => 'locale',
];
```

### Blog Posts
```php
protected $translation_model = [
    'model' => PostTranslation::class,
    'owner_key' => 'post_id',
    'slug' => 'title',
];
```

### Categories with Skip Routes
```php
protected $translation_model = [
    'model' => CategoryTranslation::class,
    'owner_key' => 'category_id',
    'skip_routes' => ['categories.quick-update'], // Don't auto-save here
];
```

## How It Works

1. **Automatic Saving**: When you save a model, the `saved` event triggers translation management
2. **Request Extraction**: Translations are extracted from the request using the configured key
3. **Language Validation**: Each language ID is validated against the languages table (cached)
4. **Slug Generation**: If configured, unique slugs are generated from the specified field
5. **Locale Storage**: If enabled, language codes are automatically stored
6. **Cascade Delete**: When a model is deleted, all translations are removed

## Performance

- **Language Caching**: Language lookups are cached in memory to reduce queries
- **Bulk Operations**: Use `withTranslations()` for batch inserts/updates
- **Eager Loading**: Load translations with `->with('translationRelation')`

## Example Application

Check the `/example` directory for a complete working Laravel application with:
- Categories (basic usage)
- Products (with slug generation)
- Posts (with SEO fields)
- Multiple languages
- CRUD operations

## Requirements

- PHP >= 8.0
- Laravel >= 8.0

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository at [https://github.com/EbrahimHanna580/translation-manager](https://github.com/EbrahimHanna580/translation-manager)
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure:
- Code follows existing patterns and conventions
- Add tests for new features
- Update documentation as needed

## Security

If you discover any security-related issues, please report them by emailing ebrahimhanna580@gmail.com instead of using the issue tracker.

## Credits

- [Ebrahim Hanna](https://github.com/EbrahimHanna580)
- [All Contributors](https://github.com/EbrahimHanna580/translation-manager/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Support

- **Issues**: [GitHub Issues](https://github.com/EbrahimHanna580/translation-manager/issues)
- **Discussions**: [GitHub Discussions](https://github.com/EbrahimHanna580/translation-manager/discussions)
- **Email**: ebrahimhanna580@gmail.com
