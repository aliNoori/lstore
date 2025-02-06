<?php

namespace App\Models;

use App\Traits\ImageManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Category extends Model
{
    use HasFactory,ImageManager;


    protected $fillable=['name','parent_id'];

    public function image():MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {

        return $this->hasMany(Product::class);
    }
    // برای دسترسی به والد
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // برای دسترسی به زیرشاخه‌ها
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
