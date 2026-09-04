<?php

declare(strict_types=1);

namespace App\Livewire\Forms\People\Traits;

trait HasSolarBirthDate
{
    /**
     * Convert the displayed solar/lunar parts into the values stored by Person.
     *
     * @return array{yob: ?int, dob: ?string}
     */
    public function resolveSolarBirth(): array
    {
        $year = $this->datePart($this->solar_year);
        $month = $this->datePart($this->solar_month);
        $day = $this->datePart($this->solar_day);

        $dob = null;
        if ($year !== null && $month !== null && $day !== null && checkdate($month, $day, $year)) {
            $dob = sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        return [
            'yob' => $year ?? $this->datePart($this->dob_year),
            'dob' => $dob,
        ];
    }

    public function assertSolarBirthValid(): void
    {
        $year = $this->datePart($this->solar_year);
        $month = $this->datePart($this->solar_month);
        $day = $this->datePart($this->solar_day);

        if (($month !== null || $day !== null) && $year === null) {
            $this->addError('solar_year', '填写阳历月或日时请同时填写年份。');
        }

        if ($year !== null && $month !== null && $day !== null && ! checkdate($month, $day, $year)) {
            $this->addError('solar_day', '阳历年月日不是有效日期。');
        }
    }

    public function birthDateRules(): array
    {
        return [
            'solar_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'solar_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'solar_day' => ['nullable', 'integer', 'min:1', 'max:31'],

            'dob_year' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'dob_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'dob_day' => ['nullable', 'integer', 'min:1', 'max:30'],
            'dob_leap' => ['boolean'],
        ];
    }

    public function birthDateValidationAttributes(): array
    {
        return [
            'solar_year' => '阳历出生年',
            'solar_month' => '阳历出生月',
            'solar_day' => '阳历出生日',
            'dob_year' => '农历出生年',
            'dob_month' => '农历出生月',
            'dob_day' => '农历出生日',
            'dob_leap' => '闰月',
        ];
    }

    public function hasBirthDateInput(): bool
    {
        return $this->datePart($this->solar_year) !== null
            || $this->datePart($this->solar_month) !== null
            || $this->datePart($this->solar_day) !== null
            || $this->datePart($this->dob_year) !== null
            || $this->datePart($this->dob_month) !== null
            || $this->datePart($this->dob_day) !== null
            || (bool) $this->dob_leap;
    }

    private function datePart(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
