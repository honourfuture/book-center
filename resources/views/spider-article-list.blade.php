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
                $article_ids = implode(',', array_unique(array_column( $source_article_group_source, 'article_id')));
            ?>
                {{$source}} ({{count($source_article_group_source)}})
                <textarea class="textarea textarea-info w-full" placeholder="Bio">{{$article_ids}}</textarea>
        @endforeach

        <table class="table w-full">
            <thead>
            <tr>
                <th>ID</th>
                <th>书名</th>
                <th>明细统计</th>
                <th>来源</th>
                <th>地址</th>
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
                    $style = "";
                    $local_article_ids = isset($bind_sources['local']) ? $bind_sources['local'] : [];
                    if(in_array($article_log->article_id, $local_article_ids)){
                        $style = "text-red-600";
                    }
                    $color_style = "";
                    if($article_log->is_proofread){
                        $color_style = "text-green-600";
                    }

                ?>
                <tr>
                    <th ><span class="{{$style}}">{{$article_log->article_id}}</span></th>
                    <td class="{{$color_style}}">
                        <span>{{$article_log->articlename ? $article_log->articlename : '-'}}</span>
                        <p class="font-light text-sm">{{$article_log->lastchapter}}</p>
                        <p class="font-light text-sm">{{$article_log->lastupdate ? date('Y-m-d H:i:s', $article_log->lastupdate) : '-'}}</p>
                        <p class="font-light text-sm">{{$article_log->author}}</p>
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
                        <button class="btn btn-xs">
                            <a href="{{route('create-source', $article_log->article_id)}}" target="_blank">source</a>
                        </button>
                        <button class="btn btn-xs">
                            <a href="/do-low-article/{{$article_log->article_id}}" target="_blank">低质量</a>
                        </button>
                    </td>
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
                            @foreach($source_article_groups[$md5] as $key => $source_article_group)
                                <?php
                                    if($source_article_group->source == 'mayi'){
                                        $source_article_group->origin_url = str_replace('m.', 'www.', $source_article_group->origin_url);
                                    }
                                    if($source_article_group->source == 'biqu789'){
                                        $source_article_group->origin_url = str_replace('net', 'com', $source_article_group->origin_url);
                                    }
                                    $style = '';
                                    if(in_array($source_article_group->article_id, $bind_sources[$source_article_group->source])){
                                        $style = 'btn-success';
                                    }
                                ?>
                                <a href="{{$source_article_group->origin_url}}" target="_blank"><button class="btn btn-xs {{$style}}">
                                    {{$source_article_group->source}}
                                </button>
                                </a>
                                <?php
                                    if(($key+1) % 3 == 0){
                                        echo "<br/>";
                                    }
                                ?>
                            @endforeach
                        @endif
                    </td>

                </tr>
            @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>

    </div>

@endsection
