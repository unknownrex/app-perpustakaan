<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    
    public function rules(): array
    {
        // Ambil ID dari route parameter ('member') jika sedang melakukan update
        $memberId = $this->route('member');

        return [
            'nama' => 'required|string|max:200',
            'nim' => 'required|string|max:12|unique:members,nim,' . $memberId,
            'email' => 'required|email|max:100|unique:members,email,' . $memberId,
            'nomor_telepon' => 'required|string|max:100',
            'alamat' => 'required|string|max:200',
            'status' => 'required|string|in:aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama member wajib diisi.',
            'nama.max' => 'Nama member maksimal 200 karakter.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.max' => 'NIM maksimal 12 karakter.',
            'nim.unique' => 'NIM ini sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'nomor_telepon.max' => 'Nomor telepon maksimal 100 karakter.',
            'alamat.max' => 'Alamat maksimal 20 karakter.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus berupa "aktif" atau "nonaktif".',
        ];
    }
}
