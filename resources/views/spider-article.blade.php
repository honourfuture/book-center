@extends('layouts.app')

@section('content')
    <title>{{$article ? $article->articlename : '-'}}</title>

    <div class="overflow-x-auto">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="/search-spider">蜘蛛分析</a></li>
                <li>{{$article ? $article->articlename : '-'}}</li>
            </ul>
        </div>
        <table class="table table-xs w-full">
            <thead>
            <tr>
                <th>remote_addr</th>
                <th>source</th>
                <th>remote_user</th>
                <th>time</th>
                <th>request</th>
                <th>url</th>
                <th>http</th>
                <th>status</th>
                <th>bytes_sent</th>
                <th>http_referer</th>
                <th>http_user_agent</th>
            </tr>
            </thead>
            <tbody>
            @foreach($article_logs as $log)
                <tr>
                    <th>{{$log->remote_addr}}</th>
                    <th>{{$log->remote_user}}</th>
                    <th>{{$log->source}}</th>
                    <th>{{$log->time}}</th>
                    <th>{{$log->request}}</th>
                    <th>
                        <a href="{{ config('app.target_url') }}{{$log->url}}" target="_blank">{{$log->url}}</a>
                    </th>
                    <th>{{$log->http}}</th>
                    <th>{{$log->status}}</th>
                    <th>{{$log->bytes_sent}}</th>
                    <th>{{$log->http_referer}}</th>
                    <th>{{$log->http_user_agent}}</th>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>

@endsection
