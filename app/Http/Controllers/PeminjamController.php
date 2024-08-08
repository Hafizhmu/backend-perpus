<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePeminjamRequest;
use App\Http\Requests\UpdatePeminjamRequest;

class PeminjamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);  // Default limit is 10
        $page = $request->query('page', 1);     // Default page is 1
        $peminjaman = Peminjam::paginate($limit);

        return response()->json([
            'status' => 'berhasil mendapatkan daftar peminjaman',
            'error' => false,
            'data' => $peminjaman,
            'page' => $peminjaman->currentPage(),    // Current page number
            'limit' => $peminjaman->perPage(),       // Number of items per page
            'totalRecords' => $peminjaman->total(),  // Total number of records
            'totalPages' => $peminjaman->lastPage()
        ], 200);
    }

    public function riwayatPeminjaman(Request $request)
    {
        $limit = $request->query('limit', 10);  // Default limit is 10
        $page = $request->query('page', 1);
        // Perform a raw query to get borrowing history for the specific book
        $riwayat = DB::table('peminjams')
            ->join('bukus', 'peminjams.id_buku', '=', 'bukus.id_buku')
            ->select(
                'peminjams.id_peminjaman',
                'peminjams.nama_peminjam',
                'peminjams.tanggal_peminjaman',
                'peminjams.tanggal_kembali_sementara',
                'peminjams.tanggal_kembali',
                'peminjams.alamat',
                'bukus.nama_buku',
                'bukus.jenis_buku',
                'bukus.tema'
            )
            ->where('bukus.id_buku', $request->input('id_buku'))
            ->orderBy('peminjams.tanggal_peminjaman', 'desc')
            ->paginate($limit);

        // Check if any borrowing records were found
        if ($riwayat->isEmpty()) {
            return response()->json([
                'status' => 'tidak ada riwayat peminjaman untuk buku ini',
                'error' => true,
                'data' => [],
                'page' => $riwayat->currentPage(),    // Current page number
                'limit' => $riwayat->perPage(),       // Number of items per page
                'totalRecords' => $riwayat->total(),  // Total number of records
                'totalPages' => $riwayat->lastPage()
            ], 404);
        }

        return response()->json([
            'status' => 'berhasil mendapatkan riwayat peminjaman buku',
            'error' => false,
            'data' => $riwayat
        ], 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeminjamRequest $request)
    {
        // Temukan buku yang dipinjam
        $buku = Buku::find($request->id_buku);

        // Periksa apakah buku ditemukan dan stok mencukupi
        if (!$buku) {
            return response()->json([
                'status' => 'buku tidak ditemukan',
                'error' => true,
                'data' => null
            ], 404);
        }

        if ($buku->jumlah_buku <= 0) {
            return response()->json([
                'status' => 'stok buku tidak mencukupi',
                'error' => true,
                'data' => null
            ], 400);
        }

        // Mulai transaksi database
        DB::beginTransaction();
        try {
            // Buat peminjaman baru
            $peminjam = Peminjam::create($request->all());

            // Kurangi stok buku
            $buku->jumlah_buku -= 1;
            $buku->save();

            // Komit transaksi
            DB::commit();

            return response()->json([
                'status' => 'berhasil menambahkan peminjaman',
                'error' => false,
                'data' => $peminjam
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            return response()->json([
                'status' => 'gagal menambahkan peminjaman',
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Peminjam $peminjam)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Peminjam $peminjam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePeminjamRequest $request, $id)
    {
        // Find the loan record
        $peminjam = Peminjam::find($id);

        // Check if tanggal_kembali is being set or updated
        if ($request->has('tanggal_kembali') && $request->input('tanggal_kembali') !== null) {
            $buku = Buku::find($peminjam->id_buku);

            // Validate the book exists
            if ($buku) {
                // Start a database transaction
                DB::beginTransaction();
                try {
                    // Update the loan record with the return date
                    $peminjam->update($request->all());

                    // Increment the book's stock
                    $buku->jumlah_buku += 1;
                    $buku->save();

                    // Commit the transaction
                    DB::commit();

                    return response()->json([
                        'status' => 'berhasil memperbarui peminjaman dan stok buku',
                        'error' => false,
                        'data' => $peminjam
                    ], 200);
                } catch (\Exception $e) {
                    // Rollback the transaction if there's an error
                    DB::rollBack();

                    return response()->json([
                        'status' => 'gagal memperbarui peminjaman',
                        'error' => true,
                        'message' => $e->getMessage()
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'buku tidak ditemukan',
                    'error' => true,
                    'data' => null
                ], 404);
            }
        } else {
            // If no return date is set, just update other fields if necessary
            $peminjam->update($request->all());

            return response()->json([
                'status' => 'berhasil memperbarui peminjaman',
                'error' => false,
                'data' => $peminjam
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peminjam $peminjam)
    {
        //
    }
}
