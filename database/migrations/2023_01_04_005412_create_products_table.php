<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('source');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->string('sku', 200)->nullable();
            $table->string('barcode', 200)->nullable();
            $table->double('price')->nullable();
            $table->integer('stock')->nullable();
            $table->string('product_id', 200)->nullable();
            $table->string('variant_id', 200)->nullable();
            $table->integer('status')->default(0);
            $table->double('price_original')->nullable();
            $table->text('product_data')->nullable();
            $table->text('stock_data')->nullable();
            $table->text('price_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->unique(['source', 'sku'], 'source');
            $table->index(['source', 'barcode'], 'source_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
