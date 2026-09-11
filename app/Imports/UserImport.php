<?php

namespace App\Imports;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Hash;
use Throwable;

class UserImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    use Importable, SkipsErrors;

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 1. Cek apakah email sudah ada
        $existingUser = User::where('email', $row['email'])->first();
        if ($existingUser) {
            // Jika sudah ada, skip baris ini (return null)
            return null;
        }

        // 2. (Opsional) Cek apakah empid sudah ada
        // if (User::where('empid', $row['empid'])->exists()) {
        //     return null;
        // }

        // Buat user baru
        $user = new User([
            'firstname' => $row['firstname'],
            'lastname'  => $row['lastname'],
            'empid'     => $row['empid'],
            'email'     => $row['email'],
            'password'  => Hash::make($row['password']),
        ]);

        return $user;
    }

    public function rules(): array
    {
        return [
            '*.firstname' => ['required', 'string'],
            '*.lastname'  => ['nullable', 'string'],
            '*.empid'     => ['required'],
            '*.email'     => ['required', 'string'], // Hapus unique agar tidak error
            '*.password'  => ['required'],
        ];
    }
}