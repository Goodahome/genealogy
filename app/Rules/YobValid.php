<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

final class YobValid implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 检查是否使用农历日期
        if (isset($this->data['dob_year'])) {
            // 如果使用农历日期，直接比较 yob 和 dob_year
            if ($value != $this->data['dob_year']) {
                $fail(__('person.yob_not_matching_dob', ['value' => $this->data['dob_year']]));
            }
            // if ($value !== date('Y', strtotime((string) $this->data['dob']))) {
            //     $fail(__('person.yob_not_matching_dob', ['value' => date('Y', strtotime((string) $this->data['dob']))]));
            // }
        } elseif (isset($this->data['dob'])) {
            // 原来的逻辑，处理公历日期
            // if ($value !== date('Y', strtotime((string) $this->data['dob']))) {
            //     $fail(__('person.yob_not_matching_dob', ['value' => date('Y', strtotime((string) $this->data['dob']))]));
            // }
            if ($value !== date('Y', strtotime((string) $this->data['dob']))) {
                $fail(__('person.yod_not_matching_dob', ['value' => $this->data['dob']]));
            }
        } elseif (isset($this->data['person'])) {
            if ($this->data['person']['dod_year']) {
                // yob may not be after dod->year
                if ($value < date('Y', strtotime((string) $this->data['person']['dod_year']))) {
                    $fail(__('person.yob_after_dod', ['value' => $this->data['person']['dod_year']]));
                }
            } elseif ($this->data['person']['yod']) {
                // yob may not be after yod
                if ($value < $this->data['person']['yod']) {
                    $fail(__('person.yob_after_yod', ['value' => $this->data['person']['yod']]));
                }
            }
        }
    }
}
