<?php

namespace Russofinn\Interactions\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class View extends Model
{
    protected $table;

    public $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = config('interactions.table_name_views');
        parent::__construct($attributes);
    }

    public function reply(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_id', 'id');
    }

    public function subject(): MorphTo
    {
        if (config('interactions.subject_returns_soft_deleted_models')) {
            return $this->morphTo()->withTrashed();
        }

        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include activities by a given causer.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model   $causer
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCausedBy(Builder $query, Model $causer): Builder
    {
        return $query
            ->where('causer_type', $causer->getMorphClass())
            ->where('causer_id', $causer->getKey());
    }

    /**
     * Scope a query to only include activities for a given subject.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model   $subject
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());
    }
}
