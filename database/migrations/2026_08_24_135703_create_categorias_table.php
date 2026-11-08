<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        $agora = now();

        DB::table('categorias')->insert([
            ['nome' => 'Casa', 'slug' => 'casa', 'created_at' => $agora, 'updated_at' => $agora],
            ['nome' => 'Apartamento', 'slug' => 'apartamento', 'created_at' => $agora, 'updated_at' => $agora],
            ['nome' => 'Terreno', 'slug' => 'terreno', 'created_at' => $agora, 'updated_at' => $agora],
            ['nome' => 'Sala Comercial', 'slug' => 'sala-comercial', 'created_at' => $agora, 'updated_at' => $agora],
            ['nome' => 'Chácara', 'slug' => 'chacara', 'created_at' => $agora, 'updated_at' => $agora],
            ['nome' => 'Cobertura', 'slug' => 'cobertura', 'created_at' => $agora, 'updated_at' => $agora],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
