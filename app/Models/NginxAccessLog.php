<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NginxAccessLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public function article()
    {
        return $this->hasOne(Article::class, 'articleid', 'article_id');
    }

    public function count_access_logs()
    {
        return $this->hasMany(self::class, 'article_id', 'article_id')->select([
            'article_id',
            DB::raw('count(article_id) as total'),
            'source'
        ])->groupBy(['article_id', 'source']);
    }

    public function access_logs()
    {
        return $this->hasMany(self::class, 'article_id', 'article_id');
    }
}
