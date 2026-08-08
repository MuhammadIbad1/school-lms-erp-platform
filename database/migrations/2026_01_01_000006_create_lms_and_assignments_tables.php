<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teacher_profiles')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type', 20)->nullable(); // pdf, docx, etc.
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teacher_profiles')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('attachment_path')->nullable();
            $table->dateTime('due_date');
            $table->integer('max_marks')->default(100);
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('student_profiles')->onDelete('cascade');
            $table->string('file_path');
            $table->dateTime('submitted_at');
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['submitted', 'late', 'graded', 'resubmit'])->default('submitted');
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('study_materials');
    }
};
