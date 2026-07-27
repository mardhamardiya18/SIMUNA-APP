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
        Schema::create('immunization_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('child_name');
            $table->string('head_of_family');
            $table->string('father_job')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('gender'); // 'laki-laki', 'perempuan'
            $table->string('age_text'); // e.g., '1 tahun', '12 bulan'
            $table->date('birth_date');
            $table->text('address');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('immunization_status'); // 'lengkap', 'tidak lengkap'
            $table->json('immunization_types')->nullable(); // array of string
            $table->text('incomplete_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunization_records');
    }
};
