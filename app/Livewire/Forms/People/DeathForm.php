<?php

declare(strict_types=1);

namespace App\Livewire\Forms\People;

use App\Livewire\Forms\People\Traits\HasSolarDeathDate;
use App\Rules\DodValid;
use App\Rules\YodValid;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class DeathForm extends Form
{
    use HasSolarDeathDate;

    // -----------------------------------------------------------------------
    #[Validate]
    public $yod;

    #[Validate]
    public $dod;

    // 阳历死亡日期（界面用年月日；保存时映射为 dod / yod）
    public $solar_year;

    public $solar_month;

    public $solar_day;

    public $pod;

    public $cemetery_location_name;

    public $cemetery_location_address;

    public $cemetery_location_latitude;

    public $cemetery_location_longitude;

    // 添加农历死亡日期字段
    public ?int $dod_year = null;
    public ?int $dod_month = null;
    public ?int $dod_day = null;
    public bool $dod_leap = false;

    // -----------------------------------------------------------------------
    public function rules(): array
    {
        return array_merge([
            'yod' => [
                'nullable',
                'integer',
                'min:1',
                'max:' . date('Y'),
                new YodValid,
            ],
            'dod' => [
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:today',
                new DodValid,
            ],
            'pod'                         => ['nullable', 'string', 'max:255'],
            'cemetery_location_name'      => ['nullable', 'string', 'max:255'],
            'cemetery_location_address'   => ['nullable', 'string', 'max:255'],
            'cemetery_location_latitude'  => ['nullable', 'numeric', 'decimal:0,13', 'min:-90', 'max:90', 'required_with:cemetery_location_longitude'],
            'cemetery_location_longitude' => ['nullable', 'numeric', 'decimal:0,13', 'min:-180', 'max:180', 'required_with:cemetery_location_latitude'],
        ], $this->deathDateRules());
    }

    public function messages(): array
    {
        return [];
    }

    public function validationAttributes(): array
    {
        return array_merge([
            'yod'                         => __('person.yod'),
            'dod'                         => __('person.dod'),
            'pod'                         => __('person.pod'),
            'cemetery_location_name'      => __('metadata.location_name'),
            'cemetery_location_address'   => __('metadata.address'),
            'cemetery_location_latitude'  => __('metadata.latitude'),
            'cemetery_location_longitude' => __('metadata.longitude'),
        ], $this->deathDateValidationAttributes());
    }
}
