<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('name', 100); // e.g. "Mid-Term Examination 2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_number', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('grade_rules', function (Blueprint $table) {
            $table->id();
            $table->string('grade_name', 10); // e.g. "A+", "A", "B", "C", "D", "F"
            $table->decimal('min_percentage', 5, 2);
            $table->decimal('max_percentage', 5, 2);
            $table->decimal('grade_point', 3, 2);
            $table->string('remarks', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('student_profiles')->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2)->default(0.00);
            $table->string('remarks')->nullable();
            $table->foreignId('entered_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['exam_id', 'subject_id', 'student_id'], 'unique_student_exam_mark');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
        Schema::dropIfExists('grade_rules');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exams');
    }
};
