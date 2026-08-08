<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // e.g. "2026-2027"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // e.g. "Grade 10"
            $table->integer('numeric_code');
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('name', 50); // e.g. "Section A"
            $table->integer('capacity')->default(40);
            $table->foreignId('class_teacher_id')->nullable()->constrained('teacher_profiles')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('code', 20);
            $table->integer('pass_marks')->default(40);
            $table->integer('max_marks')->default(100);
            $table->enum('type', ['theory', 'practical', 'both'])->default('theory');
            $table->timestamps();
        });

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('parent_profiles')->nullOnDelete();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('roll_number', 50);
            $table->string('admission_number', 50)->unique();
            $table->date('admission_date');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('blood_group', 10)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teacher_profiles')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['teacher_id', 'subject_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subject');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('academic_years');
    }
};
