<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterFix extends Model
{
    use HasFactory;

    protected $table = 'jieqi_chapter_fixes';

    protected $primaryKey = 'id';

    protected $guarded = [];
}
