<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
        * @return void
        */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // nombre del producto
            $table->decimal('price', 8, 2);        // costo del producto
            $table->unsignedInteger('stock');      // cantidad del producto
            $table->enum('status', ['disponible', 'no_disponible'])
                ->default('disponible');         // estado
            $table->text('description')->nullable();
            $table->string('image_path')->nullable(); // ruta de la imagen
            $table->timestamps();
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
}
