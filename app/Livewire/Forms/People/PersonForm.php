<?php

declare(strict_types=1);

namespace App\Livewire\Forms\People;

use App\Models\Gender;
use App\Rules\DobValid;
use App\Rules\YobValid;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class PersonForm extends Form
{
    // -----------------------------------------------------------------------
    public $firstname;

    public $surname;

    public $birthname;

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
        return $rules = [
            'firstname' => ['nullable', 'string', 'max:255'],
            'surname'   => ['required', 'string', 'max:255'],
            'birthname' => ['nullable', 'string', 'max:255'],
            'nickname'  => ['nullable', 'string', 'max:255'],

            'sex'       => ['required', 'in:m,f'],
            'gender_id' => ['nullable', 'integer'],

            'yob' => [
                'nullable',
                'integer',
                'min:1',
                'max:' . date('Y'),
                new YobValid,
            ],
            'dob' => [
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:today',
                new DobValid,
            ],
            'pob' => ['nullable', 'string', 'max:255'],

            'photo' => ['nullable', 'string', 'max:255'],

            // 农历日期验证规则
            'dob_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'dob_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'dob_day' => ['nullable', 'integer', 'min:1', 'max:30'],
            'dob_leap' => ['boolean'],
            
            'dod_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'dod_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'dod_day' => ['nullable', 'integer', 'min:1', 'max:30'],
            'dod_leap' => ['boolean'],
        ];
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
        return [
            'firstname' => __('person.firstname'),
            'surname'   => __('person.surname'),
            'birthname' => __('person.birthname'),
            'nickname'  => __('person.nickname'),

            'sex'       => __('person.sex'),
            'gender_id' => __('person.gender'),

            'yob' => __('person.yob'),
            'dob' => __('person.dob'),
            'pob' => __('person.pob'),

            'photo' => __('person.photo'),

            'dob_year' => '农历出生年',
            'dob_month' => '农历出生月',
            'dob_day' => '农历出生日',
            'dob_leap' => '闰月',
            
            'dod_year' => '农历死亡年',
            'dod_month' => '农历死亡月',
            'dod_day' => '农历死亡日',
            'dod_leap' => '闰月',
        ];
    }
}
