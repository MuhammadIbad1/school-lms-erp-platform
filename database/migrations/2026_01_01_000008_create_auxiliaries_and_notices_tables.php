<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Library Subsystem
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn', 50)->unique();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('rack_number', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->decimal('fine_amount', 8, 2)->default(0.00);
            $table->enum('status', ['issued', 'returned', 'lost'])->default('issued');
            $table->timestamps();
        });

        // 2. Transport Subsystem
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('fare', 8, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number', 50)->unique();
            $table->string('driver_name', 100);
            $table->string('driver_phone', 50);
            $table->integer('capacity')->default(30);
            $table->timestamps();
        });

        Schema::create('student_transport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('student_profiles')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('transport_routes')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Hostel Subsystem
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['boys', 'girls', 'mixed'])->default('boys');
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
            $table->string('room_number', 50);
            $table->integer('capacity')->default(2);
            $table->decimal('cost_per_bed', 8, 2);
            $table->timestamps();
        });

        Schema::create('student_hostel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('student_profiles')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('hostel_rooms')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Inventory Subsystem
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('inventory_categories')->onDelete('cascade');
            $table->string('item_name');
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // 5. Notices & Announcements
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('target_role', ['all', 'teacher', 'student', 'parent', 'admin'])->default('all');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_categories');
        Schema::dropIfExists('student_hostel');
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostels');
        Schema::dropIfExists('student_transport');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('book_issues');
        Schema::dropIfExists('books');
    }
};
