<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_no')->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('cnic')->nullable();
            $table->string('gender');
            $table->unsignedTinyInteger('age');
            $table->string('city')->default('Multan');
            $table->string('referred_by')->nullable();
            $table->timestamps();
        });
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category');
            $table->unsignedInteger('price');
            $table->string('sample_type');
            $table->string('turnaround')->default('Same day');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'sample_collected', 'processing', 'completed'])->default('pending');
            $table->unsignedInteger('total');
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('paid')->default(0);
            $table->string('payment_method')->default('Cash');
            $table->dateTime('reported_at')->nullable();
            $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_id')->constrained();
            $table->unsignedInteger('price');
            $table->string('result')->nullable();
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->enum('flag', ['normal', 'high', 'low'])->default('normal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('lab_tests');
        Schema::dropIfExists('patients');
    }
};
