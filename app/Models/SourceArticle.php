<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceArticle extends Model
{
    protected $table = 'source_articles';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $guarded = [];


}
