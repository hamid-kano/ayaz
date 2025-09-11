<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 15, 6)->change();
        });
        
        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('amount', 15, 6)->change();
        });
        
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('amount', 15, 6)->change();
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
        });
        
        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
    }
};