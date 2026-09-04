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

final class PersonForm extends Form
{
    use HasSolarBirthDate;

    // -----------------------------------------------------------------------
    public $firstname;

    public $surname;

    public $occupation;

    public $nickname;

    public $sex;

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
    
    public $dod_year;
    public $dod_month;
    public $dod_day;
    public $dod_leap = false;

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
            'surname'   => ['required', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'nickname'  => ['nullable', 'string', 'max:255'],

            'sex'       => ['required', 'in:m,f'],
            'gender_id' => ['nullable', 'integer'],

            'pob' => ['nullable', 'string', 'max:255'],

            'photo' => ['nullable', 'string', 'max:255'],

            'dod_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'dod_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'dod_day' => ['nullable', 'integer', 'min:1', 'max:30'],
            'dod_leap' => ['boolean'],
        ], $this->birthDateRules());
    }

    public function messages(): array
    {
        return [
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

            'sex'       => __('person.sex'),
            'gender_id' => __('person.gender'),

            'yob' => __('person.yob'),
            'dob' => __('person.dob'),
            'pob' => __('person.pob'),

            'photo' => __('person.photo'),

            'dod_year' => '农历死亡年',
            'dod_month' => '农历死亡月',
            'dod_day' => '农历死亡日',
            'dod_leap' => '闰月',
        ], $this->birthDateValidationAttributes());
    }
}
