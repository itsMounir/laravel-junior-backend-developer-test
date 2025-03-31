<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('industry_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
