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
        Schema::create('peminjams', function (Blueprint $table) {
            $table->id('id_peminjaman');
            $table->string('nama_peminjam');
            $table->integer('id_buku');
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_kembali_sementara');
            $table->date('tanggal_kembali')->nullable();
            $table->string('alamat');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjams');
    }
};
