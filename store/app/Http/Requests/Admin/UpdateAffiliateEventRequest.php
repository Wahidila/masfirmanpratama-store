<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAffiliateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'reward_note' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,active,ended'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul event wajib diisi.',
            'title.max' => 'Judul event maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'starts_at.required' => 'Tanggal mulai wajib diisi.',
            'starts_at.date' => 'Format tanggal mulai tidak valid.',
            'ends_at.required' => 'Tanggal selesai wajib diisi.',
            'ends_at.date' => 'Format tanggal selesai tidak valid.',
            'ends_at.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'reward_note.max' => 'Catatan reward maksimal 500 karakter.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}
