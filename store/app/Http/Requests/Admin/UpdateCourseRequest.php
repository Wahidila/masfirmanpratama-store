<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    /**
     * Auto-fill slug dari title kalau kosong, normalisasi,
     * cast checkbox, explode textarea, dan filter array kosong
     * sebelum validation.
     */
    protected function prepareForValidation(): void
    {
        $title = (string) $this->input('title', '');
        $slugInput = (string) $this->input('slug', '');

        $slug = $slugInput !== '' ? $slugInput : $title;
        $slug = Str::slug($slug);

        $data = [
            'slug' => $slug,
            'meta_title' => $this->input('meta_title') ?: null,
            'meta_description' => $this->input('meta_description') ?: null,
            'installment_available' => $this->has('installment_available') && $this->input('installment_available') === '1',
        ];

        $rawDesc = $this->input('description_raw');
        if (is_string($rawDesc) && $rawDesc !== '') {
            $data['description'] = array_values(array_filter(
                explode("\n\n", $rawDesc),
                fn ($v) => trim($v) !== '',
            ));
        }

        $rawSyllabus = $this->input('syllabus_raw');
        if (is_string($rawSyllabus) && $rawSyllabus !== '') {
            $data['syllabus'] = array_values(array_filter(
                explode("\n", $rawSyllabus),
                fn ($v) => trim($v) !== '',
            ));
        }

        if ($this->has('schedule')) {
            $data['schedule'] = array_values(array_filter(
                $this->input('schedule', []),
                fn ($v) => isset($v['title']) && trim((string) $v['title']) !== '',
            ));
        }

        if ($this->has('benefits')) {
            $data['benefits'] = array_values(array_filter(
                $this->input('benefits', []),
                fn ($v) => isset($v['title']) && trim((string) $v['title']) !== '',
            ));
        }

        if ($this->has('testimonials')) {
            $data['testimonials'] = array_values(array_filter(
                $this->input('testimonials', []),
                fn ($v) => isset($v['name']) && trim((string) $v['name']) !== '',
            ));
        }

        $this->merge($data);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $courseId = $this->route('course')?->id;

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => [
                'required',
                'string',
                'max:200',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('courses', 'slug')
                    ->ignore($courseId)
                    ->whereNull('deleted_at'),
            ],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'original_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['required', Rule::in(['draft', 'active', 'archived'])],
            'badge' => ['nullable', 'string', 'max:80'],
            'badge_icon' => ['nullable', 'string', 'max:40'],
            'category_label' => ['nullable', 'string', 'max:80'],
            'rating' => ['nullable', 'string', 'max:20'],
            'student_count' => ['nullable', 'string', 'max:30'],
            'tagline' => ['nullable', 'string', 'max:300'],
            'installment_available' => ['boolean'],
            'image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
                'dimensions:min_width=600,min_height=600',
            ],
            'remove_image' => ['nullable', 'boolean'],
            'description_raw' => ['nullable', 'string', 'max:8000'],
            'description' => ['nullable', 'array'],
            'description.*' => ['string', 'max:2000'],
            'syllabus_raw' => ['nullable', 'string', 'max:8000'],
            'syllabus' => ['nullable', 'array'],
            'syllabus.*' => ['string', 'max:300'],
            'schedule' => ['nullable', 'array'],
            'schedule.*.title' => ['required_with:schedule', 'string', 'max:120'],
            'schedule.*.detail' => ['nullable', 'string', 'max:300'],
            'benefits' => ['nullable', 'array'],
            'benefits.*.icon' => ['nullable', 'string', 'max:40'],
            'benefits.*.title' => ['required_with:benefits', 'string', 'max:120'],
            'benefits.*.desc' => ['nullable', 'string', 'max:300'],
            'testimonials' => ['nullable', 'array'],
            'testimonials.*.name' => ['required_with:testimonials', 'string', 'max:120'],
            'testimonials.*.role' => ['nullable', 'string', 'max:120'],
            'testimonials.*.quote' => ['nullable', 'string', 'max:500'],
            'related' => ['nullable', 'array'],
            'related.*' => ['string', 'max:200'],
            'meta_title' => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return (new StoreCourseRequest)->messages();
    }
}
