<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class GlobalFine extends Model
{
    use HasFactory;

    protected $table = 'global_fines';
    protected $fillable = [
        'organisation',
        'regulator',
        'sector',
        'region',
        'fine_amount',
        'currency',
        'fine_date',
        'law',
        'articles_breached',
        'violation_type',
        'summary',
        'badges',
        'link_to_case'
    ];
    use HasFactory;

    protected $casts = [
        'fine_date' => 'date',
        'fine_amount' => 'decimal:2'
    ];

    // Add this to $appends so it's always available
    protected $appends = ['formatted_amount'];

    public function scopeLatest($query, $limit = 10)
    {
        return $query->orderBy('fine_date', 'desc')->limit($limit);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('organisation', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%")
                  ->orWhere('articles_breached', 'like', "%{$search}%")
                  ->orWhere('law', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeFilter($query, array $filters)
    {
        return $query->when($filters['regulator'] ?? null, function($q, $regulator) {
                return $q->where('regulator', $regulator);
            })
            ->when($filters['sector'] ?? null, function($q, $sector) {
                return $q->where('sector', $sector);
            })
            ->when($filters['year'] ?? null, function($q, $year) {
                return $q->whereYear('fine_date', $year);
            })
            ->when($filters['violation_type'] ?? null, function($q, $type) {
                return $q->where('violation_type', $type);
            });
    }

    // Accessor for formatted amount
    public function getFormattedAmountAttribute()
    {
        $symbols = [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'AUD' => 'A$',
            'CAD' => 'C$'
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;

        $amount = (float) $this->fine_amount;

        if ($amount >= 1000000000) {
            return $symbol . number_format($amount / 1000000000, 1) . 'B';
        } elseif ($amount >= 1000000) {
            return $symbol . number_format($amount / 1000000, 1) . 'M';
        } elseif ($amount >= 1000) {
            return $symbol . number_format($amount / 1000, 0) . 'K';
        }

        return $symbol . number_format($amount, 0);
    }

    public function getBadgesArrayAttribute()
    {
        return $this->badges ? explode(',', $this->badges) : [];
    }
}
