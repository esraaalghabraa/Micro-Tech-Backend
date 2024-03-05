<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title',30);
            $table->text('description');
            $table->string('functionality')->nullable();
            $table->string('category')->nullable();
            $table->string('cover')->nullable();
            $table->string('logo')->nullable();
            $table->text('about')->nullable();
            $table->text('advantages')->nullable();
            $table->text('links')->nullable();
            $table->integer('likes')->nullable()->default(0);
            $table->boolean('active')->default(0);
                $table->boolean('special')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
