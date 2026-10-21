<?php

namespace App\Http\Requests;

use App\Drink;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuestRequest extends FormRequest
{
    /**
     * Anyone with the invitation link may sign up.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'drink' => ['required', Rule::enum(Drink::class)],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Впишите имя.',
            'first_name.max' => 'Имя должно быть не длиннее 60 символов.',
            'last_name.required' => 'Впишите фамилию.',
            'last_name.max' => 'Фамилия должна быть не длиннее 60 символов.',
            'drink.required' => 'Выберите напиток.',
            'drink.enum' => 'Выберите напиток из списка.',
        ];
    }
}
