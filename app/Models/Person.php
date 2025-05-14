<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Countries;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Korridor\LaravelHasManyMerged\HasManyMerged;
use Korridor\LaravelHasManyMerged\HasManyMergedRelation;

final class Person extends Model implements HasMedia
{
    use HasManyMergedRelation;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'firstname',
        'surname',
        'birthname',
        'nickname',

        'sex',
        'gender_id',

        'father_id',
        'mother_id',
        'parents_id',

        'dob', 'yob', 'pob',
        'dod', 'yod', 'pod',

        'summary',

        'street', 'number',
        'postal_code', 'city',
        'province', 'state',
        'country',
        'phone',

        'photo',

        'team_id',

        'dob_lunar_year',
        'dob_lunar_month',
        'dob_lunar_day',
        'dob_lunar_leap',
        'dod_lunar_year',
        'dod_lunar_month',
        'dod_lunar_day',
        'dod_lunar_leap',
    ];

    protected $appends = [
        'name',
    ];

    /* -------------------------------------------------------------------------------------------- */
    // Log activities
    /* -------------------------------------------------------------------------------------------- */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('person_couple')
            ->setDescriptionForEvent(fn (string $eventName): string => __('person.person') . ' ' . __('app.event_' . $eventName))
            ->logOnly([
                'firstname',
                'surname',
                'birthname',
                'nickname',

                'sex',
                'gender.name',

                'father.name',
                'mother.name',
                'parents.name',

                'dob', 'yob', 'pob',
                'dod', 'yod', 'pod',

                'summary',

                'street', 'number',
                'postal_code', 'city',
                'province', 'state',
                'country',
                'phone',

                'photo',

                'team.name',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        $activity->team_id = auth()->user()?->currentTeam?->id ?? null;
    }

    /* -------------------------------------------------------------------------------------------- */
    // Scopes (local)
    // The system wil look up every word in the search value in the attributes surname, firstname, birthname and nickname
    // Begin the search string with % if you want to search parts of names, for instance %Jr.
    // Be aware that this kinds of searches are slower.
    // If a name containes any spaces, enclose the name in double quoutes, for instance "John Jr." Kennedy.
    /* -------------------------------------------------------------------------------------------- */
    #[Scope]
    public function scopeSearch(Builder $query, string $searchString): void
    {
        if ($searchString !== '%') {
            collect(str_getcsv($searchString, ' ', '"'))->filter()->each(function (string $searchTerm) use ($query): void {
                $query->whereAny(['firstname', 'surname', 'birthname', 'nickname'], 'like', $searchTerm . '%');
            });
        }
    }

    #[Scope]
    public function scopeOlderThan(Builder $query, ?string $birth_year): void
    {
        if ($birth_year !== null) {
            $birth_year = (int) $birth_year;

            $query
                ->where(function ($q) use ($birth_year): void {
                    $q->whereNull('dob')->orWhere(DB::raw('YEAR(dob)'), '<=', $birth_year);
                })
                ->where(function ($q) use ($birth_year): void {
                    $q->whereNull('yob')->orWhere('yob', '<=', $birth_year);
                });
        }
    }

    #[Scope]
    public function scopeYoungerThan(Builder $query, ?string $birth_year): void
    {
        if ($birth_year !== null) {
            $birth_year = (int) $birth_year;

            $query
                ->where(function ($q) use ($birth_year): void {
                    $q->whereNull('dob')->orWhere(DB::raw('YEAR(dob)'), '>=', $birth_year);
                })
                ->where(function ($q) use ($birth_year): void {
                    $q->whereNull('yob')->orWhere('yob', '>=', $birth_year);
                });
        }
    }

    #[Scope]
    public function scopePartnerOffset(Builder $query, ?string $birth_year, int $offset = 40): void
    {
        // ------------------------------------------------------------------------
        // offset : possible partners can be +/- n ($offset) years older or younger
        // ------------------------------------------------------------------------
        if ($birth_year !== null) {
            $birth_year = (int) $birth_year;
            $min_age    = $birth_year - $offset;
            $max_age    = $birth_year + $offset;

            $query
                ->where(function ($q) use ($min_age, $max_age): void {
                    $q->whereNull('dob')->orWhereBetween(DB::raw('YEAR(dob)'), [$min_age, $max_age]);
                })
                ->where(function ($q) use ($min_age, $max_age): void {
                    $q->whereNull('yob')->orWhereBetween('yob', [$min_age, $max_age]);
                });
        }
    }

    public function countFiles(): int
    {
        return $this->getMedia('files') ? $this->getMedia('files')->count() : 0;
    }

    public function countPhotos(): int
    {
        // Define the path
        $directory = public_path('storage/photos/' . $this->team_id);

        // Check if the directory exists
        if (! File::exists($directory)) {
            return 0;
        }

        // Get all files matching the pattern
        $files = File::glob($directory . '/' . $this->id . '_*.webp');

        // Count and return the number of matching files
        return count($files);
    }

    public function isDeceased(): bool
    {
        return ! is_null($this->dod) || ! is_null($this->yod);
    }

    public function isDeletable(): bool
    {
        return $this->children->isEmpty() && $this->couples->isEmpty();
    }

    public function isBirthdayToday(): bool
    {
        return $this->dob && Carbon::parse($this->dob)->isBirthday();
    }

    public function isDeathdayToday(): bool
    {
        return $this->dod && Carbon::parse($this->dod)->isBirthday();
    }

    /* -------------------------------------------------------------------------------------------- */
    // Relations
    /* -------------------------------------------------------------------------------------------- */
    /* returns TEAM (1 Team) based on team_id */
    public function team(): BelongsTo
    {
        return $this->BelongsTo(Team::class);
    }

    /* lookup table */
    public function gender(): BelongsTo
    {
        return $this->BelongsTo(Gender::class);
    }

    /* returns FATHER (1 Person) based on father_id */
    public function father(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /* returns MOTHER (1 Person) based on mother_id */
    public function mother(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /* returns PARENTS (1 Couple) based on parents_id */
    public function parents(): BelongsTo
    {
        return $this->belongsTo(Couple::class)->with(['person_1', 'person_2']);
    }

    /* returns OWN NATURAL CHILDREN (n Person) based on father_id OR mother_id, ordered by dob */
    public function children(): HasManyMerged
    {
        return $this->HasManyMerged(self::class, ['father_id', 'mother_id'])->orderBy('dob');
    }

    public function children_with_children(): HasManyMerged // only used in family chart
    {
        return $this->HasManyMerged(self::class, ['father_id', 'mother_id'])->with('children')->orderBy('dob');
    }

    /* returns ALL NATURAL CHILDREN (n Person) (OWN + CURRENT PARTNER), ordered by type, birthyear */
    public function childrenNaturalAll(): Collection
    {
        $children_natural = $this->children;
        $children_partner = $this->currentPartner()?->children ?: collect([]);

        return $children_natural->merge($children_partner)->map(function (Person $child) use ($children_natural, $children_partner): mixed {
            $child['type'] = $children_natural->contains('id', $child->id) ? null : ($children_partner->contains('id', $child->id) ? '+' : null);

            return $child;
        })->sortBy(['birthYear', 'type']);
    }

    /* returns ALL PARTNERS (n Person) related to the person, ordered by date_start 配偶查询 */
    public function getPartnersAttribute(): Collection
    {
        if (! array_key_exists('partners', $this->relations)) {
            $partners = $this->belongsToMany(self::class, 'couples', 'person1_id', 'person2_id')
                ->withPivot(['id', 'date_start', 'date_end', 'is_married', 'has_ended'])
                ->with('children')
                ->orderByPivot('date_start')
                ->get()
                ->merge(
                    $this->belongsToMany(self::class, 'couples', 'person2_id', 'person1_id')
                        ->withPivot(['id', 'date_start', 'date_end', 'is_married', 'has_ended'])
                        ->with('children')
                        ->orderByPivot('date_start')
                        ->get()
                );

            $this->setRelation('partners', $partners);
        }

        return $this->getRelation('partners');
    }

    /* returns CURRENT PARTNER (1 Person) related to the person, where relation not_ended and date_end === null */
    public function currentPartner(): ?self
    {
        return $this->partners->where('pivot.has_ended', false)->whereNull('pivot.date_end')->sortBy('pivot.date_start')->last();
    }

    /* returns ALL PARTNERSHIPS (n Couple) related to the person, ordered by date_start 配偶方法 */
    public function couples(): HasManyMerged
    {
        return $this->HasManyMerged(Couple::class, ['person1_id', 'person2_id'])->with(['person_1', 'person_2']);
    }

    /* returns ALL METADATA (n PersonMetadata) related to the person */
    public function metadata(): HasMany
    {
        return $this->hasMany(PersonMetadata::class);
    }

    /* returns 1 METADATA (1 PersonMetadata value) related to the person */
    public function getMetadataValue($key = null): ?string
    {
        if ($key) {
            $metadata = $this->metadata->firstWhere('key', $key);

            return $metadata ? $metadata->value : null;
        }

        return null;
    }

    /* updates, deletes if empty or creates 1 to n METADATA related to the person */
    public function updateMetadata(Collection $personMetadata): void
    {
        // First, delete any existing metadata where the value is empty
        foreach (PersonMetadata::METADATA_KEYS as $key) {
            if ($personMetadata->has($key) && empty($personMetadata->get($key))) {
                PersonMetadata::where('person_id', $this->id)
                    ->where('key', $key)
                    ->delete();
            }
        }

        // Collect data to update or create
        $data = [];
        foreach (PersonMetadata::METADATA_KEYS as $key) {
            if ($personMetadata->has($key) && ! empty($personMetadata->get($key))) {
                $data[] = [
                    'person_id' => $this->id,
                    'key'       => $key,
                    'value'     => $personMetadata->get($key),
                ];
            }
        }

        // Perform bulk insert or update if there is data
        if ($data !== []) {
            PersonMetadata::upsert($data, ['person_id', 'key'], ['value']);
        }
    }

    /* returns ALL SIBLINGS (n Person) related to the person, either through father_id, mother_id or parents_id ordered by type, birthyear */
    public function siblings(bool $withChildren = false): Collection
    {
        // Check if there are any parent identifiers to avoid unnecessary queries
        if (! $this->father_id && ! $this->mother_id && ! $this->parents_id) {
            return collect([]);
        }

        // Prepare the query conditionally based on $withChildren
        $query = (fn ($column, $id) => Person::where('id', '!=', $this->id)
            ->where($column, $id)
            ->when($withChildren, fn ($q) => $q->with('children'))
            ->get());

        // Get siblings from each parent or both parents
        $siblings_father  = $this->father_id ? $query('father_id', $this->father_id) : collect([]);
        $siblings_mother  = $this->mother_id ? $query('mother_id', $this->mother_id) : collect([]);
        $siblings_parents = $this->parents_id ? $query('parents_id', $this->parents_id) : collect([]);

        // Merge the results and ensure no duplicate siblings are included
        $siblings = $siblings_father->merge($siblings_mother)->merge($siblings_parents)->unique('id');

        return $siblings->map(function (Person $sibling) use ($siblings_father, $siblings_mother, $siblings_parents): Person {
            // Determine the sibling's type based on the shared parent(s)
            if ($siblings_father->contains('id', $sibling->id) && $siblings_mother->contains('id', $sibling->id)) {
                $sibling['type'] = ''; // Full siblings (same mother and father)
            } elseif ($siblings_father->contains('id', $sibling->id) || $siblings_mother->contains('id', $sibling->id)) {
                $sibling['type'] = '[1/2]'; // Half siblings (same father or mother)
            } elseif ($siblings_parents->contains('id', $sibling->id)) {
                $sibling['type'] = '[+]'; // Step-siblings or other variations
            }

            return $sibling;
        })->sortBy(['birthYear', 'type']);
    }

    /* -------------------------------------------------------------------------------------------- */
    // Scopes (global)
    /* -------------------------------------------------------------------------------------------- */
    #[Override]
    protected static function booted(): void
    {
        self::addGlobalScope('team', function (Builder $builder): void {
            if (Auth()->guest() || Auth()->user()->is_developer) {
                return;
            }

            $builder->where('people.team_id', Auth()->user()->currentTeam->id);
        });
    }

    protected function casts(): array
    {
        return [
            'dob' => 'date:Y-m-d',
            'dod' => 'date:Y-m-d',
        ];
    }

    /* -------------------------------------------------------------------------------------------- */
    // Accessors & Mutators
    /* -------------------------------------------------------------------------------------------- */
    protected function getNameAttribute(): ?string
    {
        $name = mb_trim("{$this->firstname} {$this->surname}");

        return $name ?: null;
    }

    protected function getAgeAttribute(): ?int
    {
        if ($this->dob) {
            if ($this->dod) {
                // deceased based on dob & dod
                $age = (int) Carbon::parse($this->dob)->diffInYears($this->dod);
            } elseif ($this->yod) {
                // deceased based on dob & yod
                $age = $this->yod - Carbon::parse($this->dob)->format('Y');
            } else {
                // living
                $age = (int) Carbon::parse($this->dob)->diffInYears();
            }
        } elseif ($this->yob) {
            if ($this->dod) {
                // deceased based on yob & dod
                $age = Carbon::parse($this->dod)->format('Y') - $this->yod;
            } elseif ($this->yod) {
                // deceased based on yob & yod
                $age = $this->yod - $this->yob;
            } else {
                // living
                $age = Carbon::today()->format('Y') - $this->yob;
            }
        } else {
            $age = null;
        }

        return $age > 0 ? $age : null;
    }

    protected function getNextBirthdayAttribute(): ?Carbon
    {
        if ($this->dob) {
            $today               = Carbon::today();
            $this_years_birthday = Carbon::parse(date('Y') . mb_substr((string) ($this->dob), 4));

            return $today->gt($this_years_birthday) ? $this_years_birthday->copy()->addYear() : $this_years_birthday;
        }

        return null;
    }

    protected function getNextBirthdayAgeAttribute(): ?int
    {
        return $this->dob ? Carbon::parse($this->dob)->age + 1 : null;
    }

    protected function getNextBirthdayRemainingDaysAttribute(): ?int
    {
        if (! $this->dob) {
            return null;
        }

        $today            = Carbon::today();
        $birthdayThisYear = Carbon::parse($this->dob)->year($today->year);

        // If the birthday is today, return 0 days remaining
        if ($birthdayThisYear->isToday()) {
            return 0;
        }

        // Determine if the next birthday is this year or next year
        $nextBirthday = $birthdayThisYear->isPast() ? $birthdayThisYear->addYear() : $birthdayThisYear;

        return (int) $today->diffInDays($nextBirthday, false);
    }

    protected function getLifetimeAttribute(): ?string
    {
        if ($this->dob) {
            if ($this->dod) {
                // deceased based on dob & dod
                $lifetime = Carbon::parse($this->dob)->format('Y') . ' - ' . Carbon::parse($this->dod)->format('Y');
            } elseif ($this->yod) {
                // deceased based on dob & yod
                $lifetime = Carbon::parse($this->dob)->format('Y') . ' - ' . $this->yod;
            } else {
                // living
                $lifetime = Carbon::parse($this->dob)->format('Y');
            }
        } elseif ($this->yob) {
            if ($this->dod) {
                // deceased based on yob & dod
                $lifetime = $this->yod . ' - ' . Carbon::parse($this->dod)->format('Y');
            } elseif ($this->yod) {
                // deceased based on yob & yod
                $lifetime = $this->yob . ' - ' . $this->yod;
            } else {
                // living
                $lifetime = (string) ($this->yob);
            }
        } else {
            $lifetime = null;
        }

        return $lifetime ? $lifetime : null; // returns YEAR(dob) - YEAR(dod) or null
    }

    protected function getBirthYearAttribute(): ?string
    {
        if ($this->dob) {
            $year = Carbon::parse($this->dob)->format('Y');
        } elseif ($this->yob) {
            $year = $this->yob;
        } else {
            $year = null;
        }

        return (string) $year;
    }

    protected function getBirthFormattedAttribute(): ?string
    {
        if ($this->dob) {
            $birth = Carbon::parse($this->dob)->timezone(session('timezone') ?? 'UTC')->isoFormat('LL');
        } elseif ($this->yob) {
            $birth = $this->yob;
        } else {
            $birth = null;
        }

        return (string) $birth;
    }

    protected function getDeathFormattedAttribute(): ?string
    {
        if ($this->dod) {
            $dead = Carbon::parse($this->dod)->timezone(session('timezone') ?? 'UTC')->isoFormat('LL');
        } elseif ($this->yod) {
            $dead = $this->yod;
        } else {
            $dead = null;
        }

        return (string) $dead;
    }

    protected function getAddressAttribute(): ?string
    {
        $countries = new Countries(app()->getLocale());

        $components = [
            mb_trim("{$this->street} {$this->number}"),
            mb_trim("{$this->postal_code} {$this->city}"),
            mb_trim("{$this->province} {$this->state}"),
            $this->country ? $countries->getCountryName($this->country) : null,
        ];

        // Filter empty components and implode with newline characters.
        $address = implode("\n", array_filter($components));

        return $address ?: null;
    }

    /**
     * Get the full Google Maps address URL.
     */
    protected function getAddressGoogleAttribute(): ?string
    {
        $countries         = new Countries(app()->getLocale());
        $hrefGoogleAddress = 'https://www.google.com/maps/search/';

        $components = [
            mb_trim("{$this->street} {$this->number}"),
            mb_trim("{$this->postal_code} {$this->city}"),
            mb_trim("{$this->province} {$this->state}"),
            $this->country ? $countries->getCountryName($this->country) : null,
        ];

        // Filter empty components, implode with commas, and URL-encode the address.
        $address = implode(',', array: array_filter(array : $components));

        return $address !== '' ? $hrefGoogleAddress . urlencode($address) : null;
    }

    protected function getCemeteryGoogleAttribute(): ?string
    {
        $hrefGoogleGeo     = 'https://www.google.com/maps/search/?api=1&query=';
        $hrefGoogleAddress = 'https://www.google.com/maps/search/';

        $latitude  = $this->getMetadataValue('cemetery_location_latitude');
        $longitude = $this->getMetadataValue('cemetery_location_longitude');
        $address   = $this->getMetadataValue('cemetery_location_address');

        if ($latitude && $longitude) {
            return $hrefGoogleGeo . urlencode("{$latitude},{$longitude}");
        }
        if ($address) {
            return $hrefGoogleAddress . urlencode(str_replace("\n", ',', $address));
        }

        return null;
    }
    /**
     * 获取格式化的农历出生日期
     */
    public function getLunarBirthFormattedAttribute(): ?string
    {
        if ($this->dob_lunar_year && $this->dob_lunar_month && $this->dob_lunar_day) {
            $chineseMonths = [
                1 => '正月', 2 => '二月', 3 => '三月', 4 => '四月', 
                5 => '五月', 6 => '六月', 7 => '七月', 8 => '八月', 
                9 => '九月', 10 => '十月', 11 => '冬月', 12 => '腊月'
            ];
            
            $chineseDays = [
                1 => '初一', 2 => '初二', 3 => '初三', 4 => '初四', 5 => '初五',
                6 => '初六', 7 => '初七', 8 => '初八', 9 => '初九', 10 => '初十',
                11 => '十一', 12 => '十二', 13 => '十三', 14 => '十四', 15 => '十五',
                16 => '十六', 17 => '十七', 18 => '十八', 19 => '十九', 20 => '二十',
                21 => '廿一', 22 => '廿二', 23 => '廿三', 24 => '廿四', 25 => '廿五',
                26 => '廿六', 27 => '廿七', 28 => '廿八', 29 => '廿九', 30 => '三十'
            ];
            
            // 转换年份为中文大写
            $chineseYear = '';
            $yearStr = (string)$this->dob_lunar_year;
            $chineseNumbers = [
                '0' => '零', '1' => '一', '2' => '二', '3' => '三', '4' => '四', 
                '5' => '五', '6' => '六', '7' => '七', '8' => '八', '9' => '九'
            ];
            
            for ($i = 0; $i < strlen($yearStr); $i++) {
                $chineseYear .= $chineseNumbers[$yearStr[$i]];
            }
            
            $month = $chineseMonths[$this->dob_lunar_month] ?? $this->dob_lunar_month.'月';
            $day = $chineseDays[$this->dob_lunar_day] ?? $this->dob_lunar_day.'日';
            $leapText = $this->dob_lunar_leap ? '闰' : '';
            
            return "{$chineseYear}年{$leapText}{$month}{$day}";
        }
        return null;
    }
    /**
     * 获取格式化的农历死亡日期
     */
    protected function getLunarDeathFormattedAttribute(): ?string
    {
        if ($this->dod_lunar_year && $this->dod_lunar_month && $this->dod_lunar_day) {
            $chineseMonths = [
                1 => '正月', 2 => '二月', 3 => '三月', 4 => '四月',
                5 => '五月', 6 => '六月', 7 => '七月', 8 => '八月',
                9 => '九月', 10 => '十月', 11 => '冬月', 12 => '腊月'
            ];
            $chineseDays = [
                1 => '初一', 2 => '初二', 3 => '初三', 4 => '初四', 5 => '初五',
                6 => '初六', 7 => '初七', 8 => '初八', 9 => '初九', 10 => '初十',
                11 => '十一', 12 => '十二', 13 => '十三', 14 => '十四', 15 => '十五',
                16 => '十六', 17 => '十七', 18 => '十八', 19 => '十九', 20 => '二十',
                21 => '廿一', 22 => '廿二', 23 => '廿三', 24 => '廿四', 25 => '廿五',
                26 => '廿六', 27 => '廿七', 28 => '廿八', 29 => '廿九', 30 => '三十'
            ];
            $chineseYear = '';
            $yearStr = (string)$this->dod_lunar_year; // Fixed
            $chineseNumbers = [
                '0' => '零', '1' => '一', '2' => '二', '3' => '三', '4' => '四',
                '5' => '五', '6' => '六', '7' => '七', '8' => '八', '9' => '九'
            ];
            for ($i = 0; $i < strlen($yearStr); $i++) {
                $chineseYear .= $chineseNumbers[$yearStr[$i]];
            }
            $month = $chineseMonths[$this->dod_lunar_month] ?? $this->dod_lunar_month.'月';
            $day = $chineseDays[$this->dod_lunar_day] ?? $this->dod_lunar_day.'日';
            $leapText = $this->dod_lunar_leap ? '闰' : '';
            return "{$chineseYear}年{$leapText}{$month}{$day}";
        }
        return null;
    }
}
