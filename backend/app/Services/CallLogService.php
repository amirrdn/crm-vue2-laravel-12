<?php

namespace App\Services;

use App\Models\CallLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CallLogService
{
    public function getAll(array $filters = [])
    {
        $query = CallLog::with(['contact']);

        if (isset($filters['contact_id'])) {
            $query->where('contact_id', $filters['contact_id']);
        }

        return $query->get();
    }

    public function getById(int $id)
    {
        return CallLog::with(['contact'])->findOrFail($id);
    }

    public function create(array $data)
    {
        $validator = Validator::make($data, [
            'contact_id' => 'required|exists:contacts,id',
            'started_at' => 'required|date',
            'duration' => 'required|integer|min:0',
            'status' => 'required|in:completed,missed,failed'
        ], [
            'contact_id.required' => 'Contact ID is required',
            'contact_id.exists' => 'Contact not found',
            'started_at.required' => 'Call start time is required',
            'started_at.date' => 'Invalid call start time format',
            'duration.required' => 'Duration is required',
            'duration.integer' => 'Duration must be a number',
            'duration.min' => 'Duration must be at least 0',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return CallLog::create($data);
    }

    public function update(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'contact_id' => 'sometimes|required|exists:contacts,id',
            'started_at' => 'sometimes|required|date',
            'duration' => 'sometimes|required|integer|min:0',
            'status' => 'sometimes|required|in:completed,missed,failed'
        ], [
            'contact_id.required' => 'ID Kontak harus diisi',
            'contact_id.exists' => 'Kontak tidak ditemukan',
            'started_at.required' => 'Waktu mulai panggilan harus diisi',
            'started_at.date' => 'Format waktu mulai panggilan tidak valid',
            'duration.required' => 'Durasi harus diisi',
            'duration.integer' => 'Durasi harus berupa angka',
            'duration.min' => 'Durasi minimal 0',
            'status.required' => 'Status harus diisi',
            'status.in' => 'Status tidak valid'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $callLog = CallLog::findOrFail($id);
        $callLog->update($data);
        return $callLog;
    }

    public function delete(int $id)
    {
        $callLog = CallLog::findOrFail($id);
        $callLog->delete();
        return true;
    }
} 