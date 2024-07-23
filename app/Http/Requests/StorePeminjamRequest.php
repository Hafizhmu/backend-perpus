<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_peminjam' => 'required|string|max:255',
            'id_buku' => 'required|exists:buku,id_buku',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_kembali_sementara' => 'required|date|after_or_equal:tanggal_peminjaman',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_peminjaman',
            'alamat' => 'required|string|max:255',
        ];
    }
}
