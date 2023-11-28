@extends('layouts.app')
@section('content')
    <title>蜘蛛趋势分析</title>

    <div class="overflow-x-auto">
        <div class="text-sm breadcrumbs">
            <ul>
                <li>蜘蛛趋势分析({{count($spider_articles)}})</li>
            </ul>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-xs">
                <thead>
                <tr>
                    <th>Name</th>
                    @foreach($date_total as $day => $total)
                        <th>{{$day}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($spider_articles as $article_id => $spider_article)
                    <tr>
                    <td class="">
                        <a href="{{route('check-error-article', ['id' => $article_id])}}" target="_blank" class="text-sm">
                            {{isset($spider_article['article']) ? $spider_article['article']['articlename'] : '-'}}
                            @if(isset($spider_article['article']))
                                {{$spider_article['article']['fullflag'] ? '完本' : ''}}
                            @endif
                        </a>

                        <p class="font-light text-xs">{{isset($spider_article['article']) ? $spider_article['article']['lastchapter'] : '-'}}</p>
                        <p class="font-light text-xs">{{isset($spider_article['article']) ? date('Y-m-d H:i:s', $spider_article['article']['lastupdate']) : '-'}}</p>
                        <p class="font-light text-xs">{{isset($spider_article['article']) ? $spider_article['article']['author'] : '-'}}</p>
                    </td>
                    @foreach($spider_article['total'] as $day => $total)

                        <td>
                            <?php
                                $next = next($spider_article['total']);
                                if ($total > $next) {
                                    $arrowClass = 'arrow-up red';
                                } elseif ($total < $next) {
                                    $arrowClass = 'arrow-down green';
                                } else{
                                    $arrowClass = '';
                                }
                            ?>

                            {{$total}} <span class="{{ $arrowClass }}">
                        </td>
                    @endforeach
                </tr>
                @endforeach

                </tbody>
            </table>
        </div>

    </div>

@endsection
