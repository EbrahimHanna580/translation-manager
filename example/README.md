# Laravel Example App

## Overview

This is a simple Laravel application that demonstrates the use of models and migrations to manage languages, categories,
and their translations.

## Models

### 1. Language

- **Model Name**: `Language`
- **Creating Command**: php artisan make:model Language -m
- **Fields**:
    - `title`: The name of the language (string).
    - `code`: The language code (string, e.g., "en", "ar").

### 2. Category

- **Model Name**: `Category`
- **Creating Command**: php artisan make:model Category -m
- **Fields**:
    - `name`: The name of the category (string).

### 3. Categories Translation

- **Model Name**: `Categories Translation`
- **Creating Command**: php artisan make:model CategoriesTranslation -m
- **Fields**:
    - `language_id`: Foreign key referencing the `languages` table.
    - `model_id`: Foreign key referencing the `categories` table.
    - `name`: The translated name of the category (string).

## Installation

1. Install dependencies:
    ```bash
    composer install
    ```

2. Set up your .env file:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

3. Run migrations:
    ```bash
    php artisan migrate --seed
    ```
## Usage

1. in <b>Category</b> model add <b>HasTranslations</b> and <b>$translation_model</b> property
   
    ```php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use EbrahimHanna\TranslationManager\Traits\HasTranslations;

    class Category extends Model
    {
       use HasTranslations;

       protected $translation_model = [
            'model' => CategoriesTranslation::class,
            'owner_key' => 'category_id',
        //  'foreign_key' => 'language_id',
        //  'translations_data_key' => 'translations',
        //  'locale_column' => 'locale',
        //  'language_code_column' => 'code',
        //  'slug' => 'name',

        //  By default the foreign key name is "language_id"
        //  and the owner_key name is "model_id"

        //  If you would like to replace it for all models
        //  you can change it in config/laravel-translations
        ];
    ```


## Blade Form

The Blade form for adding category translations should include the following fields:

- `name_en`: English translation of the category name (string).
- `name_ar`: Arabic translation of the category name (string).

Example Blade form:

```blade
<form action="{{ route('categories.store') }}" method="POST">
    @csrf
    <label for="name_en">English Name:</label>
    <input type="text" name="name_en" required>

    <label for="name_ar">Arabic Name:</label>
    <input type="text" name="name_ar" required>

    <button type="submit">Submit</button>
</form>
```

## Contributing
Contributions are welcome! Please fork the repository and submit a pull request.

## License
This example project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
