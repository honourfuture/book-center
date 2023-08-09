@extends('layouts.app')
<title>手动更新</title>

@section('content')
    <div class="text-sm breadcrumbs">
        <ul>
            <li>手动更新</li>
        </ul>
    </div>
    <div class="overflow-x-auto">
        <table class="table table-xs">
            <thead>
            <tr>
                <th>ID</th>
                <th>书名</th>
                <th>搜索引擎来源</th>
                <th>书源</th>
                <th>地址</th>
                <th>其他地址</th>
            </tr>
            </thead>
            <tbody>
            @foreach($articles as $article)
                <tr>
                    <th>{{$article->article_id}}</th>
                    <td>{{$article->article_name}}</td>
                    <td>{{$article->source}}</td>
                    <td>{{$article->origin_article}}</td>
                    <td>
                        <?php
                            $index = intval($article->article_id / 1000);
                            $local_url = "https://www.tieshuw.com/{$index}_{$article->article_id}/";
                            $backend_url = "https://www.tieshuw.com/modules/article/articlemanage.php?id={$article->article_id}";
                            $check_url = "http://help.tieshuw.com/article/{$article->article_id}";
                        ?>

                            <button class="btn btn-xs">
                                <a href="{{$local_url}}" target="_blank">站内url</a>
                            </button>
                            <button class="btn btn-xs">
                                <a href="{{$backend_url}}" target="_blank">后台地址</a>
                            </button>
                            <button class="btn btn-xs">
                                <a href="{{$check_url}}" target="_blank">检测地址</a>
                            </button>
                    </td>
                    <td>

                        <?php echo $article['69shu'] ?>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>

@endsection
