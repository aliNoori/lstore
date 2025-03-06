<?php

namespace App\Models;

use App\Traits\ImageManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PaymentMethod extends Model
{
    use HasFactory,ImageManager;

    protected $fillable=['name','description','is_active','type'];

    public function image():MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
