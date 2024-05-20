<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('no action');
            $table->string('name', 255);
            $table->text('description');
            $table->integer('price');
            $table->string('image', 255);
            $table->date('expired_at');
            $table->string('modified_by', 255)->comment('email_user');
            $table->foreign('modified_by')->references('email')->on('users')->onDelete('no action')->onUpdate('no action');
            $table->timestamps();
        });
    }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};