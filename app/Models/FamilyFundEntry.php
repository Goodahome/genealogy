<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class FamilyFundEntry extends Model
{
    public const TYPE_INCOME = 'income';

    public const TYPE_EXPENSE = 'expense';

    protected $fillable = [
        'team_id',
        'created_by',
        'person_id',
        'type',
        'category',
        'amount',
        'occurred_on',
        'title',
        'contributor',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'occurred_on' => 'date',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function isIncome(): bool
    {
        return $this->type === self::TYPE_INCOME;
    }

    public function isExpense(): bool
    {
        return $this->type === self::TYPE_EXPENSE;
    }

    public function signedAmount(): float
    {
        return $this->isIncome()
            ? (float) $this->amount
            : -(float) $this->amount;
    }

    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public static function incomeCategories(): array
    {
        return [
            'donation' => __('fund.categories.donation'),
            'interest' => __('fund.categories.interest'),
            'rent' => __('fund.categories.rent'),
            'other_income' => __('fund.categories.other_income'),
        ];
    }

    public static function expenseCategories(): array
    {
        return [
            'ancestral_rite' => __('fund.categories.ancestral_rite'),
            'genealogy' => __('fund.categories.genealogy'),
            'ancestral_hall' => __('fund.categories.ancestral_hall'),
            'scholarship' => __('fund.categories.scholarship'),
            'relief' => __('fund.categories.relief'),
            'meeting' => __('fund.categories.meeting'),
            'other_expense' => __('fund.categories.other_expense'),
        ];
    }

    public static function categoriesForType(string $type): array
    {
        return $type === self::TYPE_INCOME
            ? self::incomeCategories()
            : self::expenseCategories();
    }

    public function categoryLabel(): string
    {
        return self::incomeCategories()[$this->category]
            ?? self::expenseCategories()[$this->category]
            ?? $this->category;
    }
}
