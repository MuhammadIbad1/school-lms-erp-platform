<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE fee_invoices MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE fee_invoices MODIFY COLUMN status ENUM('unpaid', 'partially_paid', 'paid') NOT NULL DEFAULT 'unpaid'");
    }
};
