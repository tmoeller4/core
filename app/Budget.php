<?php

namespace Biigle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description', 
        'amount',
        'spent',
        'currency',
        'start_date',
        'end_date',
        'active',
        'project_id',
        'creator_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'spent' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean',
    ];

    /**
     * The project that this budget belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The user who created this budget.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the remaining budget amount.
     *
     * @return float
     */
    public function getRemainingAttribute()
    {
        return $this->amount - $this->spent;
    }

    /**
     * Get the budget utilization percentage.
     *
     * @return float
     */
    public function getUtilizationPercentageAttribute()
    {
        if ($this->amount == 0) {
            return 0;
        }
        
        return round(($this->spent / $this->amount) * 100, 2);
    }

    /**
     * Check if the budget is currently active.
     *
     * @return bool
     */
    public function isActive()
    {
        if (!$this->active) {
            return false;
        }

        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Check if the budget is overrun.
     *
     * @return bool
     */
    public function isOverrun()
    {
        return $this->spent > $this->amount;
    }

    /**
     * Scope a query to only include active budgets.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include current budgets (within date range).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }
}