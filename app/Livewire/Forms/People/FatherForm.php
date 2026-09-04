<?php

declare(strict_types=1);

namespace App\Livewire\Forms\People;

use App\Livewire\Forms\People\Traits\HasSolarBirthDate;
use App\Models\Gender;
use App\Rules\DobValid;
use App\Rules\YobValid;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class FatherForm extends Form
{
    use HasSolarBirthDate;

    // -----------------------------------------------------------------------
    public $person;

    // -----------------------------------------------------------------------
    public $firstname;

    public $surname;

    public $occupation;

    public $nickname;

    public $gender_id;

    #[Validate]
    public $yob;

    #[Validate]
    public $dob;

    public $pob;

    public $photo;

    // 农历日期字段
    public $dob_year;
    public $dob_month;
    public $dob_day;
    public $dob_leap = false;

    public $solar_year;
    public $solar_month;
    public $solar_day;

    // -----------------------------------------------------------------------
    public $person_id;

    // -----------------------------------------------------------------------
    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function genders(): Collection
    {
        return Gender::select('id', 'name')->orderBy('name')->get();
    }

    // -----------------------------------------------------------------------
    public function rules(): array
    {
        return array_merge([
            'firstname' => ['nullable', 'string', 'max:255'],
            'surname'   => ['nullable', 'string', 'max:255', 'required_without:person_id'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'nickname'  => ['nullable', 'string', 'max:255'],

            'gender_id' => ['nullable', 'integer'],

            'pob' => ['nullable', 'string', 'max:255'],

            'photo' => ['nullable', 'string', 'max:255'],

            // -----------------------------------------------------------------------
            'person_id' => ['nullable', 'integer', 'required_without:surname'],

        ], $this->birthDateRules());
    }

    public function messages(): array
    {
        return [
            'surname.required_without'   => __('validation.surname.required_without'),
            'person_id.required_without' => __('validation.person_id.required_without'),
            'dob_year.required' => '请选择农历出生年份',
            'dob_month.required' => '请选择农历出生月份',
            'dob_day.required' => '请选择农历出生日期',
        ];
    }

    public function validationAttributes(): array
    {
        return array_merge([
            'firstname' => __('person.firstname'),
            'surname'   => __('person.surname'),
            'occupation' => __('person.occupation'),
            'nickname'  => __('person.nickname'),

            'gender_id' => __('person.gender'),

            'yob' => __('person.yob'),
            'dob' => __('person.dob'),
            'pob' => __('person.pob'),

            'photo' => __('person.photo'),

            'person_id' => __('person.person'),

        ], $this->birthDateValidationAttributes());
    }
}
