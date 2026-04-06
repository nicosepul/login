<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar columnas si existen
            if (Schema::hasColumn('users', 'clinic_id')) {
                $table->dropColumn('clinic_id');
            }

            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropColumn('role_id');
            }

            // Nuevos campos
            $table->string('direccion')->nullable()->after('email');
            $table->string('genero')->nullable()->after('direccion');
            $table->date('fecha_nacimiento')->nullable()->after('genero');

            // Guardar numericCode de país
            $table->string('nacionalidad')->nullable()->after('fecha_nacimiento');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();

            $table->dropColumn([
                'direccion',
                'genero',
                'fecha_nacimiento',
                'nacionalidad',
            ]);
        });
    }
};