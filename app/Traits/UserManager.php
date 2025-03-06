<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Http\Request;

trait UserManager
{
    public function getUser($request)
    {

        return $user=$request->user();

    }
}
