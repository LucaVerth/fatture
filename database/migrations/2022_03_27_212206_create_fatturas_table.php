<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFatturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fatturas', function (Blueprint $table) {
            $table->id();
            $table->string('file_name', 192)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->integer('file_size')->length(11)->nullable($value = true);
            $table->string('file_hash', 384)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->string('hash_type', 30)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->string('file_extension', 30)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->string('file_encoding', 75)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->string('file_format', 150)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->timestamp('dataIns');
            $table->string('NumeroFattura', 60)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->date('DataFattura')->nullable($value = true);
            $table->string('RagSocDest', 300)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->string('CodFiscDest', 48)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->string('PIvaDest', 84)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->decimal('Importo', 13,0)->nullable($value = true);
            $table->decimal('imposta', 13,0)->nullable($value = true);
            $table->string('EsigIva', 15)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->binary('file_path')->nullable($value = true);
            $table->string('TipoFattura', 30)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->string('TipoDoc', 75)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->string('Nazione', 15)->collation('utf8mb4_unicode_ci')->nullable($value = true);
            $table->integer('Anno')->length(5)->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fatturas');
    }
}
