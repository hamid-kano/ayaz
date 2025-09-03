<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('currency', ['syp', 'usd']);
            $table->date('purchase_date');
            $table->enum('status', ['cash', 'debt'])->default('cash');
            $table->text('details');
            $table->string('supplier');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchases');
    }
};