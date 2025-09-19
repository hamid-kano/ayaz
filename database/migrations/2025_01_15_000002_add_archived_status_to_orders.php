<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // تحديث enum لإضافة حالة 'archived'
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'in-progress', 'ready', 'delivered', 'archived', 'cancelled') DEFAULT 'new'");
    }

    public function down()
    {
        // إرجاع enum للحالة السابقة
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'in-progress', 'ready', 'delivered', 'cancelled') DEFAULT 'new'");
    }
};