<?php

declare(strict_types=1);

namespace App\Livewire\Forms\People;

use App\Models\Gender;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Form;

final class ProfileForm extends Form
{
    // 农历出生日期
    public $dob_year;

    public $dob_month;

    public $dob_day;

    public $dob_leap = false;

    public $dod_year;

    public $dod_month;

    public $dod_day;

    public $dod_leap = false;

    // 阳历出生日期（界面用年月日；保存时映射为 dob / yob）
    public $solar_year;

    public $solar_month;

    public $solar_day;

    public $firstname;

    public $surname;

    public $occupation;

    public $nickname;

    public $sex;

    public $gender_id;

    /** @deprecated UI 已不用，保留兼容旧校验映射 */
    public $yob;

    /** @deprecated UI 已不用，保留兼容旧校验映射 */
    public $dob;

    public $pob;

    public $summary;

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function genders(): Collection
    {
        return Gender::select('id', 'name')->orderBy('name')->get();
    }

    public function rules(): array
    {
        return [
            'firstname' => ['nullable', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],

            'sex' => ['required', 'in:m,f'],
            'gender_id' => ['nullable', 'integer'],

            'solar_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'solar_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'solar_day' => ['nullable', 'integer', 'min:1', 'max:31'],

            'pob' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:65535'],

            'dob_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'dob_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'dob_day' => ['nullable', 'integer', 'min:1', 'max:30'],
            'dob_leap' => ['boolean'],

            'dod_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'dod_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'dod_day' => ['nullable', 'integer', 'min:1', 'max:30'],
            'dod_leap' => ['boolean'],
        ];
    }

    /**
     * 由阳历年月日推导 dob（完整日期）与 yob（年份）。
     * 仅有年份时只写 yob；有完整年月日时两者都写。
     * 若阳历无年份但有农历年，则用农历年作为 yob（保持原有「大约出生年」用途）。
     *
     * @return array{yob: ?int, dob: ?string}
     */
    public function resolveSolarBirth(): array
    {
        $year = $this->solar_year !== null && $this->solar_year !== '' ? (int) $this->solar_year : null;
        $month = $this->solar_month !== null && $this->solar_month !== '' ? (int) $this->solar_month : null;
        $day = $this->solar_day !== null && $this->solar_day !== '' ? (int) $this->solar_day : null;

        $dob = null;
        if ($year !== null && $month !== null && $day !== null && checkdate($month, $day, $year)) {
            $dob = sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        $yob = $year;
        if ($yob === null && $this->dob_year !== null && $this->dob_year !== '') {
            $yob = (int) $this->dob_year;
        }

        return [
            'yob' => $yob,
            'dob' => $dob,
        ];
    }

    public function assertSolarBirthValid(): void
    {
        $year = $this->solar_year !== null && $this->solar_year !== '' ? (int) $this->solar_year : null;
        $month = $this->solar_month !== null && $this->solar_month !== '' ? (int) $this->solar_month : null;
        $day = $this->solar_day !== null && $this->solar_day !== '' ? (int) $this->solar_day : null;

        if (($month !== null || $day !== null) && $year === null) {
            $this->addError('solar_year', '填写阳历月或日时请同时填写年份。');
        }

        if ($year !== null && $month !== null && $day !== null && ! checkdate($month, $day, $year)) {
            $this->addError('solar_day', '阳历年月日不是有效日期。');
        }
    }

    public function messages(): array
    {
        return [];
    }

    public function validationAttributes(): array
    {
        return [
            'firstname' => __('person.firstname'),
            'surname' => __('person.surname'),
            'occupation' => __('person.occupation'),
            'nickname' => __('person.nickname'),

            'sex' => __('person.sex'),
            'gender_id' => __('person.gender'),

            'solar_year' => '阳历出生年',
            'solar_month' => '阳历出生月',
            'solar_day' => '阳历出生日',

            'yob' => __('person.yob'),
            'dob' => __('person.dob'),
            'pob' => __('person.pob'),

            'summary' => __('person.summary'),

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
