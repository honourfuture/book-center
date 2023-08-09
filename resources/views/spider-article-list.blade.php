@extends('layouts.app')
@section('content')
    <title>蜘蛛分析 </title>

    <div class="overflow-x-auto">
        <div class="text-sm breadcrumbs">
            <ul>
                <li>蜘蛛分析</li>
            </ul>
        </div>
        <table class="table table-xs w-full">
            <thead>
            <tr>
                <th>ID</th>
                <th>书名</th>
                <th>最后更新时间</th>
                <th>搜索引擎来源</th>
                <th>统计</th>
                <th>地址</th>
                <th>明细统计</th>
                <th>备注</th>
            </tr>
            </thead>
            <tbody>
            @foreach($article_logs as $article_log)
                <?php
                    $index = intval($article_log->article_id / 1000);
                    $local_url = "https://www.tieshuw.com/{$index}_{$article_log->article_id}/";
                    $backend_url = "https://www.tieshuw.com/modules/article/articlemanage.php?id={$article_log->article_id}";
                    $check_url = "http://help.tieshuw.com/article/{$article_log->article_id}";
                ?>
                <tr>
                    <th>{{$article_log->article_id}}</th>
                    <td>{{$article_log->article ? $article_log->article->articlename : '-'}}</td>
                    <td>{{$article_log->article ? date('Y-m-d H:i:s', $article_log->article->lastupdate) : '-'}}</td>
                    <td>{{$article_log->source}}</td>
                    <td>{{$article_log->total}}</td>
                    <td>
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
                        @foreach($article_log->count_access_logs as $count)
                            <?php
                                $search_url = "/search-spider/{$article_log->article_id}?source=".$count->source;
                            ?>
                            <div class="indicator">
                            <span class="indicator-item badge badge-primary">
                                {{$count->total}}
                            </span>
                                <button class="btn btn-xs">
                                    <a href="{{$search_url}}" target="_blank">{{$count->source}}</a>
                                </button>
                            </div>
                        @endforeach
                    </td>
                    <td>
                        {{$article_log->desc}}
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>

@endsection
