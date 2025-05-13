<?php

declare(strict_types=1);

namespace App\Livewire\Forms\People;

use App\Rules\DodValid;
use App\Rules\YodValid;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class DeathForm extends Form
{
    // -----------------------------------------------------------------------
    #[Validate]
    public $yod;

    #[Validate]
    public $dod;

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
        return [
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
            // 添加农历死亡日期验证规则
            'dod_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'dod_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'dod_day' => ['nullable', 'integer', 'min:1', 'max:30'],
            'dod_leap' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function validationAttributes(): array
    {
        return [
            'yod'                         => __('person.yod'),
            'dod'                         => __('person.dod'),
            'pod'                         => __('person.pod'),
            'cemetery_location_name'      => __('metadata.location_name'),
            'cemetery_location_address'   => __('metadata.address'),
            'cemetery_location_latitude'  => __('metadata.latitude'),
            'cemetery_location_longitude' => __('metadata.longitude'),
            // 添加农历日期字段的验证属性
            'dod_year'                    => __('person.dod_year'),
            'dod_month'                   => __('person.dod_month'),
            'dod_day'                     => __('person.dod_day'),
            'dod_leap'                    => __('person.dod_leap'),
        ];
    }
}
