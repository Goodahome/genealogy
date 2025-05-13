<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Carbon\Carbon;

class LunarDatePicker extends Component
{
    public $id;
    public $name;
    public $label;
    public $value;
    public $placeholder;
    public $required;
    public $minYear;
    public $maxYear;
    
    // 添加中文月份数组
    protected $chineseMonths = [
        1 => '正月', 2 => '二月', 3 => '三月', 4 => '四月', 
        5 => '五月', 6 => '六月', 7 => '七月', 8 => '八月', 
        9 => '九月', 10 => '十月', 11 => '冬月', 12 => '腊月'
    ];
    
    // 添加中文日期数组
    protected $chineseDays = [
        1 => '初一', 2 => '初二', 3 => '初三', 4 => '初四', 5 => '初五',
        6 => '初六', 7 => '初七', 8 => '初八', 9 => '初九', 10 => '初十',
        11 => '十一', 12 => '十二', 13 => '十三', 14 => '十四', 15 => '十五',
        16 => '十六', 17 => '十七', 18 => '十八', 19 => '十九', 20 => '二十',
        21 => '廿一', 22 => '廿二', 23 => '廿三', 24 => '廿四', 25 => '廿五',
        26 => '廿六', 27 => '廿七', 28 => '廿八', 29 => '廿九', 30 => '三十'
    ];

    // 添加中文数字数组
    protected $chineseNumbers = [
        '0' => '零', '1' => '一', '2' => '二', '3' => '三', '4' => '四', 
        '5' => '五', '6' => '六', '7' => '七', '8' => '八', '9' => '九'
    ];

    public function __construct(
        $id = null,
        $name = null,
        $label = null,
        $value = null,
        $placeholder = '请选择日期',
        $required = false,
        $minYear = 1900,
        $maxYear = 2100
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->minYear = $minYear;
        $this->maxYear = $maxYear;
    }

    public function render()
    {
        return view('components.lunar-date-picker', [
            'chineseMonths' => $this->chineseMonths,
            'chineseDays' => $this->chineseDays,
            'chineseNumbers' => $this->chineseNumbers
        ]);
    }
    
    /**
     * 将数字年份转换为中文大写
     */
    public function getChineseYear($year)
    {
        $result = '';
        $yearStr = (string)$year;
        
        for ($i = 0; $i < strlen($yearStr); $i++) {
            $result .= $this->chineseNumbers[$yearStr[$i]];
        }
        
        return $result;
    }
}