<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // تحديث enum للأدوار لإضافة دور المدقق
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'auditor') NOT NULL DEFAULT 'user'");
    }

    public function down()
    {
        // إرجاع enum للحالة السابقة
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
    }
};