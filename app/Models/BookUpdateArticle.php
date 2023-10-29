<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Libs\ReplaceModelTrait;

class BookUpdateArticle extends Model
{
    use HasFactory;
    use ReplaceModelTrait;

    public $timestamps = false;

    protected $table = 'book_update_articles';

}
