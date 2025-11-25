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
        Schema::create('animals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('sex')->default('M');
            $table->date('birthday');
            $table->string('species');
            $table->string('breed')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('doctor_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::create('animal_user', function (Blueprint $table) {
            $table->foreignUuid('animal_id')
                ->references('id')
                ->on('animals')
                ->cascadeOnDelete();

            $table->foreignUuid('owner_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->boolean('main_owner')->default(false);
            $table->primary('animal_id', 'owner_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
