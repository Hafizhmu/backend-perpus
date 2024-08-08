<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);  // Default limit is 10

        // Get filter parameters
        $jenisBuku = $request->query('jenis_buku');
        $tema = $request->query('tema');
        $tahunTerbit = $request->query('tahun_terbit');

        // Use query builder with dynamic filters
        $query = Buku::query();

        // Apply filters
        $query->when($jenisBuku, function ($query, $jenisBuku) {
            return $query->where('jenis_buku', $jenisBuku);
        });

        $query->when($tema, function ($query, $tema) {
            return $query->where('tema', $tema);
        });

        $query->when($tahunTerbit, function ($query, $tahunTerbit) {
            return $query->where('tahun_terbit', $tahunTerbit);
        });

        // Paginate the results
        $buku = $query->paginate($limit);

        return response()->json([
            'status' => 'berhasil mendapatkan Buku',
            'error' => false,
            'data' => $buku->items(),
            'page' => $buku->currentPage(),    // Current page number
            'limit' => $buku->perPage(),       // Number of items per page
            'totalRecords' => $buku->total(),  // Total number of records
            'totalPages' => $buku->lastPage()
        ], 200);
    }
    public function bukuAll(Request $request)
    {
      
        $buku = Buku::all();

        return response()->json([
            'status' => 'berhasil mendapatkan Buku',
            'error' => false,
            'data' => $buku
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBukuRequest $request)
    {
        $buku = Buku::create($request->all());

        return response()->json([
            'status' => 'berhasil menambahkan Buku',
            'error' => false,
            'data' => $buku
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Buku $buku)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buku $buku)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBukuRequest $request)
    {
        $buku = Buku::find($request->input('id'));

        if (!$buku) {
            return response()->json([
                'status' => 'Buku tidak ditemukan',
                'error' => true,
                'data' => null
            ], 404);
        }

        $buku->update($request->all());

        return response()->json([
            'status' => 'berhasil mengupdate Buku',
            'error' => false,
            'data' => $buku
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buku $buku, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json([
                'status' => 'Buku tidak ditemukan',
                'error' => true,
                'data' => null
            ], 404);
        }

        $buku->delete();

        return response()->json([
            'status' => 'berhasil menghapus Buku',
            'error' => false,
            'data' => null
        ], 200);
    }
}
