<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->string('locale')->nullable();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('content');
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['language_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts_translations');
    }
};
