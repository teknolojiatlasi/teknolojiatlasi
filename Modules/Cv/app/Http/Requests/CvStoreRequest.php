<?php
namespace Modules\Cv\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CvStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'title'     => ['nullable', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'address'   => ['nullable', 'string', 'max:255'],
            'about'     => ['nullable', 'string'],
            'photo'     => ['nullable', 'string'],
            'template'  => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Ad Soyad zorunludur',
            'email.required'    => 'E-posta zorunludur',
            'email.email'       => 'Geçerli bir e-posta giriniz',
            'template.required' => 'CV şablonu seçilmelidir',
        ];
    }
}
