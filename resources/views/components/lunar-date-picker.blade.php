<div>
    @if($label)
    <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
    @endif
    
    <div class="flex flex-wrap gap-2">
        <select id="{{ $id }}_year" wire:model="{{ $name }}_year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="">年</option>
            @for($year = $minYear; $year <= $maxYear; $year++)
                @php
                    $chineseYear = '';
                    $yearStr = (string)$year;
                    for ($i = 0; $i < strlen($yearStr); $i++) {
                        $chineseYear .= $chineseNumbers[$yearStr[$i]];
                    }
                @endphp
                <option value="{{ $year }}">{{ $chineseYear }}年</option>
            @endfor
        </select>
        
        <select id="{{ $id }}_month" wire:model="{{ $name }}_month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="">月</option>
            @foreach($chineseMonths as $num => $month)
                <option value="{{ $num }}">{{ $month }}</option>
            @endforeach
        </select>
        
        <select id="{{ $id }}_day" wire:model="{{ $name }}_day" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="">日</option>
            @foreach($chineseDays as $num => $day)
                <option value="{{ $num }}">{{ $day }}</option>
            @endforeach
        </select>
        
        <div class="flex items-center p-2 bg-yellow-50 border border-yellow-300 rounded-sm dark:bg-gray-600 dark:border-yellow-600">
            <input type="checkbox" id="{{ $id }}_leap" wire:model="{{ $name }}_leap" class="w-5 h-5 text-blue-600 bg-yellow-100 border-yellow-400 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <label for="{{ $id }}_leap" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">闰月</label>
        </div>
    </div>
</div>