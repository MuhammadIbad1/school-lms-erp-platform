<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('occupation')->nullable();
            $table->string('identity_card_no')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('employee_code')->unique();
            $table->string('qualification')->nullable();
            $table->string('designation')->default('Teacher');
            $table->date('joining_date');
            $table->decimal('basic_salary', 10, 2)->default(0.00);
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
        Schema::dropIfExists('parent_profiles');
    }
};
