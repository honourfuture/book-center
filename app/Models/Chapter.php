<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'jieqi_article_chapter';

    protected $primaryKey = 'chapterid';

}
