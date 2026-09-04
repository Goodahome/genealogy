<?php

declare(strict_types=1);

namespace App\Livewire\Forms\People\Traits;

trait HasSolarDeathDate
{
    /**
     * Convert the displayed solar/lunar parts into the values stored by Person.
     *
     * @return array{yod: ?string, dod: ?string}
     */
    public function resolveSolarDeath(): array
    {
        $year = $this->deathDatePart($this->solar_year);
        $month = $this->deathDatePart($this->solar_month);
        $day = $this->deathDatePart($this->solar_day);

        $dod = null;
        if ($year !== null && $month !== null && $day !== null && checkdate($month, $day, $year)) {
            $dod = sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        return [
            // YodValid/DodValid use strict comparison with date('Y'), so keep
            // the derived year as a string just like the legacy input did.
            'yod' => ($year ?? $this->deathDatePart($this->dod_year)) !== null
                ? (string) ($year ?? $this->deathDatePart($this->dod_year))
                : null,
            'dod' => $dod,
        ];
    }

    public function assertSolarDeathValid(): void
    {
        $year = $this->deathDatePart($this->solar_year);
        $month = $this->deathDatePart($this->solar_month);
        $day = $this->deathDatePart($this->solar_day);

        if (($month !== null || $day !== null) && $year === null) {
            $this->addError('solar_year', '填写阳历月或日时请同时填写年份。');
        }

        if ($year !== null && $month !== null && $day !== null && ! checkdate($month, $day, $year)) {
            $this->addError('solar_day', '阳历年月日不是有效日期。');
        }
    }

    public function deathDateRules(): array
    {
        return [
            'solar_year' => ['nullable', 'integer', 'min:1', 'max:' . date('Y')],
            'solar_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'solar_day' => ['nullable', 'integer', 'min:1', 'max:31'],

            'dod_year' => ['nullable', 'integer', 'min:1', 'max:' . date('Y')],
            'dod_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'dod_day' => ['nullable', 'integer', 'min:1', 'max:30'],
            'dod_leap' => ['boolean'],
        ];
    }

    public function deathDateValidationAttributes(): array
    {
        return [
            'solar_year' => '阳历死亡年',
            'solar_month' => '阳历死亡月',
            'solar_day' => '阳历死亡日',
            'dod_year' => '农历死亡年',
            'dod_month' => '农历死亡月',
            'dod_day' => '农历死亡日',
            'dod_leap' => '闰月',
        ];
    }

    private function deathDatePart(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
