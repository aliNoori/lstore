<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\ImageManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,ImageManager,HasRoles,HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the user's image.
     */
    public function image():MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }


    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function scores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Score::class);
    }
    public function coupons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Coupon::class);
    }
    public function addresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Address::class);
    }
    public function getOrdersCountAttribute(): int
    {
        return $this->orders()->count(); // محاسبه تعداد لایک‌ها
    }
    public function getScoresAttribute()
    {
        return $this->scores()->sum('score'); // محاسبه امتیازات
    }
    public function getUserRolesAttribute(): \Illuminate\Support\Collection
    {
        return $this->getRoleNames(); // Returns a collection
    }
    public function getCouponsCountAttribute(): int
    {
        return $this->coupons()->count(); // محاسبه امتیازات
    }
    public function getItemsCartAttribute(): int
    {
        $cart = $this->cart; // Assuming cart() is a relationship
        return $cart ? $cart->cartItems()->count() : 0;
    }
    public function getWalletBalanceAttribute(): int
    {
        return $this->wallet ? $this->wallet->balance : 0;
    }
}
