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
            $table->string('description',255);
            $table->string('functionality',255)->nullable();
            $table->string('cover',1000)->nullable();
            $table->string('logo',1000)->nullable();
            $table->string('about',255)->nullable();
            $table->string('advantages')->nullable();
            $table->string('links')->nullable();
            $table->boolean('active')->default(0);
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
