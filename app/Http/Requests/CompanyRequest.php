<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            'name'  => 'required|min:3',
            'category' => 'required',
            'address' => 'required',
            'phone' =>' required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama Harus Diisi',
            'address.required' => 'Alamat Harus Diisi',
            'phone.required' => 'Telepon Harus Diisi',
            'category.required' => 'Kategori Harus Diisi',
            'name.min' => 'Minimal 3 Karakter',
        ];
    }
}
