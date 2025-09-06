<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->date('order_date');
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->text('order_type');
            $table->text('order_details');
            $table->decimal('cost', 10, 2);
            $table->enum('currency', ['syp', 'usd']);
            $table->enum('status', ['new', 'in-progress', 'delivered', 'cancelled'])->default('new');
            $table->date('delivery_date');
            $table->string('reviewer_name')->nullable();
            $table->unsignedBigInteger('executor_id')->nullable();
            $table->timestamps();

            $table->foreign('executor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
