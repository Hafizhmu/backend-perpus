<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
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
            'judul_buku' => 'required|string|max:255',
            'jenis_buku' => 'required|string|max:255',
            'tema' => 'required|string|max:255',
            'posisi_buku' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1000|max:' . (date('Y') + 1),
            'jumlah_buku' => 'required|integer|min:1',
        ];
    }
}
