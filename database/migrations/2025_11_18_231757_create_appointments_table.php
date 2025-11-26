<?php

use App\Helpers\Enums\AppointmentStatus;
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
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id');
            $table->foreignUuid('animal_id');
            $table->foreignUuid('doctor_id');
            $table->longText('situation');
            $table->dateTime('scheduled_at');
            $table->enum('status', AppointmentStatus::cases())->default(AppointmentStatus::Pending);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
