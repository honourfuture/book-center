<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleVisit extends Model
{
    use HasFactory;

    protected $table = 'shipsay_article_visit';

    protected $primaryKey = 'article_id';

    public $timestamps = false;

}
