<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size'
    ];
    /**
     * Get the parent imageable model (user or post).
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
