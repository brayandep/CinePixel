<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->dateTime('start_time');
            $table->dateTime('end_time');

            $table->unsignedTinyInteger('num_people');       // 1 o 2
            $table->decimal('products_amount', 8, 2)->default(0);
            $table->enum('payment_method', ['efectivo', 'qr']);
            $table->decimal('total', 8, 2);
            $table->enum('status', ['activa', 'finalizada'])->default('activa');

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
        Schema::dropIfExists('reservations');
    }
}
