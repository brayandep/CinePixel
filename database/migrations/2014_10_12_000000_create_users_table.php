<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();

        // Datos básicos
        $table->string('name');
        $table->string('username')->unique();      // para iniciar sesión
        $table->string('email')->unique()->nullable();
        $table->string('phone')->nullable();

        // Rol dentro del sistema
        $table->enum('role', ['admin', 'empleado', 'cliente'])->default('cliente');

        // Estado del usuario
        $table->enum('status', ['activo', 'inactivo'])->default('activo');

        // Autenticación
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();

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
        Schema::dropIfExists('users');
    }
}
