<?php

namespace App\Models;

use App\Traits\ImageManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ShippingMethod extends Model
{
    use HasFactory,ImageManager;

    protected $fillable=['name','description','cost','delivery_time','is_active'];

    public function image():MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
