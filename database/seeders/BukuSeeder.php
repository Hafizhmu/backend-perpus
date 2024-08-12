<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data dari database lokal
        $bukuData = DB::connection('local')->table('bukus')->get();

        // Insert data ke dalam database produksi
        foreach ($bukuData as $buku) {
            DB::table('bukus')->insert([
                'judul_buku' => $buku->judul_buku,
                'jenis_buku' => $buku->jenis_buku,
                'tema' => $buku->tema,
                'posisi_buku' => $buku->posisi_buku,
                'penulis' => $buku->penulis,
                'tahun_terbit' => $buku->tahun_terbit,
                'jumlah_buku' => $buku->jumlah_buku
            ]);
        }
    }
}
