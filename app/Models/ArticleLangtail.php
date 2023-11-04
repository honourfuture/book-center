<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleLangtail extends Model
{
    use HasFactory;

    protected $table = 'shipsay_article_langtail';

    protected $primaryKey = 'langid';

    public $timestamps = false;

}
