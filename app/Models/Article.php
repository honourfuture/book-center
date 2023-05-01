<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'jieqi_article_article';

    protected $primaryKey = 'articleid';

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'articleid', 'articleid');
    }
}
