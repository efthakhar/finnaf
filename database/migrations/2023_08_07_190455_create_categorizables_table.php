<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categorizables', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained();
            $table->morphs('categorizable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorizables');
    }
};
