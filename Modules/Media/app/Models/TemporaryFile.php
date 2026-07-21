<?php

namespace Modules\Media\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryFile extends Model
{
    protected $fillable = ['folder', 'filename'];
}
