<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // تحديث جدول order_items لدعم الكسور في الأسعار
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
        });
        
        // تحديث جدول receipts لدعم الكسور في المبالغ
        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        
        // تحديث جدول purchases لدعم الكسور في المبالغ
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
    }

    public function down()
    {
        // العودة للحالة السابقة
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('price')->change();
        });
        
        Schema::table('receipts', function (Blueprint $table) {
            $table->integer('amount')->change();
        });
        
        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('amount')->change();
        });
    }
};