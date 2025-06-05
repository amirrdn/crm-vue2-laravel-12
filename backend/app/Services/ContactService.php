<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContactService
{
    public function getAll(array $filters = [])
    {
        $query = Contact::with(['user']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['position'])) {
            $query->where('position', $filters['position']);
        }

        if (isset($filters['favorites_only']) && $filters['favorites_only'] === true) {
            $query->where('is_favorite', true);
        }

        return $query->get();
    }

    public function getById($id)
    {
        return Contact::with(['user'])->findOrFail((int) $id);
    }

    public function create(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string',
        ], [
            'name.required' => 'Nama kontak harus diisi',
            'name.max' => 'Nama kontak maksimal 255 karakter',
            'company.required' => 'Nama perusahaan harus diisi',
            'company.max' => 'Nama perusahaan maksimal 255 karakter',
            'position.required' => 'Jabatan harus diisi',
            'position.max' => 'Jabatan maksimal 255 karakter',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.max' => 'Nomor telepon maksimal 20 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'is_primary.boolean' => 'Status kontak utama harus berupa boolean',
            'notes.string' => 'Catatan harus berupa teks',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (isset($data['is_primary']) && $data['is_primary']) {
            Contact::where('user_id', auth()->id())
                  ->where('is_primary', true)
                  ->update(['is_primary' => false]);
        }

        $data['user_id'] = auth()->id();
        return Contact::create($data);
    }

    public function update(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'company' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:255',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string',
        ], [
            'name.required' => 'Nama kontak harus diisi',
            'name.max' => 'Nama kontak maksimal 255 karakter',
            'company.required' => 'Nama perusahaan harus diisi',
            'company.max' => 'Nama perusahaan maksimal 255 karakter',
            'position.required' => 'Jabatan harus diisi',
            'position.max' => 'Jabatan maksimal 255 karakter',
            'phone.required' => 'Nomor telepon harus diisi',
            'phone.max' => 'Nomor telepon maksimal 20 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'is_primary.boolean' => 'Status kontak utama harus berupa boolean',
            'notes.string' => 'Catatan harus berupa teks',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $contact = Contact::findOrFail($id);

        if (isset($data['is_primary']) && $data['is_primary']) {
            Contact::where('user_id', auth()->id())
                  ->where('id', '!=', $id)
                  ->where('is_primary', true)
                  ->update(['is_primary' => false]);
        }

        $contact->update($data);
        return $contact;
    }

    public function delete(int $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return true;
    }

    public function toggleFavorite($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->is_favorite = !$contact->is_favorite;
        $contact->save();
        return $contact;
    }

    public function getUniquePositions()
    {
        return Contact::select('position')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->pluck('position')
            ->sort()
            ->values();
    }
} 