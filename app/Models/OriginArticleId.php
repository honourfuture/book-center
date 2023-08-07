<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OriginArticleId extends Model
{
    protected $table = 'origin_article_ids';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

}
