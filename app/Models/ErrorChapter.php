<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorChapter extends Model
{
    use HasFactory;

    protected $table = 'error_chapters';

    protected $primaryKey = 'id';
}
