<?php

namespace App\Models;

use App\Traits\ImageManager;
use App\Traits\UserManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Product extends Model
{
    use HasFactory,ImageManager,UserManager;
    protected $fillable=['sku','name','price','stock','description','discount','category_id'];

    /**
     * Get the user's image.
     */
    public function image():MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
    public function orderDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'fileable');
    }

    public function histories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {

        return $this->hasMany(History::class);
    }
    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {

        return $this->hasMany(Review::class);
    }
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count(); // محاسبه تعداد لایک‌ها
    }
    public function likes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count(); // محاسبه تعداد لایک‌ها
    }
    public function views(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(View::class);
    }

    public function getViewsCountAttribute(): int
    {
        return $this->views()->count(); // محاسبه تعداد ویوها
    }


}
