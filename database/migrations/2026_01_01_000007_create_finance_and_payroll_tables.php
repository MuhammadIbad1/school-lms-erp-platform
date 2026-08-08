<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Tuition Fee, Lab Charges, Transport Fee, Admission Fee
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('fee_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('fee_group_id')->constrained('fee_groups')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->timestamps();
        });

        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('student_profiles')->onDelete('cascade');
            $table->string('invoice_number', 100)->unique();
            $table->string('title', 255);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid');
            $table->timestamps();
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_invoice_id')->constrained('fee_invoices')->onDelete('cascade');
            $table->string('transaction_id', 100)->unique();
            $table->decimal('amount_paid', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'online'])->default('cash');
            $table->dateTime('paid_at');
            $table->string('notes')->nullable();
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teacher_profiles')->onDelete('cascade');
            $table->string('month_year', 20); // e.g. "August 2026"
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('allowances', 10, 2)->default(0.00);
            $table->decimal('deductions', 10, 2)->default(0.00);
            $table->decimal('net_salary', 10, 2);
            $table->enum('status', ['generated', 'paid'])->default('generated');
            $table->date('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_invoices');
        Schema::dropIfExists('fee_masters');
        Schema::dropIfExists('fee_groups');
    }
};
