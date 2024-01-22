<?php

namespace App\Livewire\Forms\People;

use App\Models\Gender;
use Livewire\Attributes\Computed;
use Livewire\Form;

class PersonForm extends Form
{
    // -----------------------------------------------------------------------
    public $image = null;       // file upload input

    public $iteration = 0;      // needed for reset upload input

    // -----------------------------------------------------------------------
    public $firstname;

    public $surname;

    public $birthname;

    public $nickname;

    public $sex;

    public $gender_id;

    public $yob;

    public $dob;

    public $pob;

    public $photo;

    // -----------------------------------------------------------------------
    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function genders()
    {
        return Gender::select('id', 'name')->orderBy('name')->get()->toArray();
    }

    // -----------------------------------------------------------------------
    // ToDo : add to rules : yob must be equal to dob->format("Y) when dob not null
    protected $rules = [
        'firstname' => ['nullable', 'string', 'max:255'],
        'surname' => ['required', 'string', 'max:255'],
        'birthname' => ['nullable', 'string', 'max:255'],
        'nickname' => ['nullable', 'string', 'max:255'],

        'sex' => ['required', 'in:m,f'],
        'gender_id' => ['nullable', 'integer'],

        'yob' => ['nullable', 'date_format:Y'],
        'dob' => ['nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
        'pob' => ['nullable', 'string', 'max:255'],

        'photo' => ['nullable', 'string', 'max:255'],
        'image' => ['nullable', 'sometimes', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:1024'],
    ];

    public function messages()
    {
        return [];
    }

    public function validationAttributes()
    {
        return [
            'firstname' => __('person.firstname'),
            'surname' => __('person.surname'),
            'birthname' => __('person.birthname'),
            'nickname' => __('person.nickname'),

            'sex' => __('person.sex'),
            'gender_id' => __('person.gender'),

            'yob' => __('person.yob'),
            'dob' => __('person.dob'),
            'pob' => __('person.pob'),

            'photo' => __('person.photo'),
            'image' => __('person.photo'),
        ];
    }

    // -----------------------------------------------------------------------
    public function YobCorrespondsDob()
    {
        return $this->yob && $this->dob ? $this->yob == date('Y', strtotime($this->dob)) : true;
    }
}