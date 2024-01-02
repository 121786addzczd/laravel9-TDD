<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    const OPEN = 1;
    const CLOSED = 0;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * ステータスが「公開」のポストのみをクエリに含める。
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyOpen($query)
    {
        return $query->where('status', self::OPEN);
    }

    public function isClosed()
    {
        return $this->status == self::CLOSED;
    }
}
