@extends('layouts.app')
@section('content')
    <title>蜘蛛分析</title>

    <div class="overflow-x-auto">
        <div class="text-sm breadcrumbs">
            <ul>
                <li>蜘蛛分析({{$article_logs->count()}})</li>
            </ul>
        </div>
        @foreach($source_article_group_sources as $source => $source_article_group_source)
            <?php
                $article_ids = implode(',', $source_article_group_source->pluck('article_id')->unique()->toArray());
            ?>
                {{$source}} ({{$source_article_group_source->count()}})
                <textarea class="textarea textarea-info w-full" placeholder="Bio">{{$article_ids}}</textarea>
        @endforeach
        <table class="table w-full">
            <thead>
            <tr>
                <th>ID</th>
                <th>书名</th>
                <th>作者</th>
                <th>统计</th>
                <th>地址</th>
                <th>明细统计</th>
            </tr>
            </thead>
            <tbody>
            @foreach($article_logs as $article_log)
                <?php
                    $index = intval($article_log->article_id / 1000);
                    $local_url = "https://www.tieshuw.com/{$index}_{$article_log->article_id}/";
                    $backend_url = "https://www.tieshuw.com/modules/article/articlemanage.php?id={$article_log->article_id}";
                    $check_url = "http://help.tieshuw.com/article/{$article_log->article_id}";
                    $md5 = md5($article_log->articlename .'-'. $article_log->author);
                ?>
                <tr>
                    <th>{{$article_log->article_id}}</th>
                    <td>
                        {{$article_log->articlename ? $article_log->articlename : '-'}}
                        <p class="font-light text-sm">{{$article_log->lastchapter}}</p>
                        <p class="font-light text-sm">{{$article_log->lastupdate ? date('Y-m-d H:i:s', $article_log->lastupdate) : '-'}}</p>

                    </td>
                    <td></td>
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
                        <br/>
                        @if(isset($source_article_groups[$md5]))
                            @foreach($source_article_groups[$md5] as $source_article_group)
                                <a href="{{$source_article_group->origin_url}}" target="_blank"><button class="btn btn-xs">
                                    {{$source_article_group->source}}
                                </button>
                                </a>
                            @endforeach
                        @endif
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
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>

    </div>

@endsection
