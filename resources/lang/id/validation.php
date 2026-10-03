<?php

return [
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'max' => [
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
        'file' => ':attribute tidak boleh lebih dari :max kilobyte.',
    ],
    'min' => [
        'string' => ':attribute minimal :min karakter.',
    ],
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'unique' => ':attribute sudah digunakan.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'between' => [
        'numeric' => ':attribute harus antara :min dan :max.',
    ],
    'mimes' => ':attribute harus berupa berkas bertipe: :values.',
    'mimetypes' => ':attribute harus berupa berkas bertipe: :values.',
    'file' => ':attribute harus berupa berkas.',
    'uploaded' => ':attribute gagal diunggah.',
    'current_password' => 'Password saat ini salah.',
];
