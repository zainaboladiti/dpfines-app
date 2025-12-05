<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScrapedFine extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'scraped_fines';

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
        'link_to_case',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'submitted_by',
    ];

    protected $casts = [
        'fine_date' => 'date',
        'fine_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['formatted_amount', 'badges_array'];

    /**
     * Scope to get pending reviews
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved fines
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected fines
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Relationship: User who submitted this fine
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Relationship: User who reviewed this fine
     */
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Accessor for formatted amount
     */
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

    /**
     * Accessor for badges array
     */
    public function getBadgesArrayAttribute()
    {
        return $this->badges ? explode(',', $this->badges) : [];
    }
}
