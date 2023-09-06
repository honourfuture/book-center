@extends('layouts.app')

@section('content')

    <style>
        /*reset*/
        .main-im{position:fixed;right:10px;top:300px;z-index:100;width:110px;height:272px;}
        .main-im .qq-a{display:block;width:106px;height:116px;font-size:14px;color:#0484cd;text-align:center;position:relative;}
        .main-im .qq-a span{bottom:5px;position:absolute;width:90px;left:10px;}
        .main-im .qq-hover-c{width:70px;height:70px;border-radius:35px;position:absolute;left:18px;top:10px;overflow:hidden;z-index:9;}
        .main-im .qq-container{z-index:99;position:absolute;width:109px;height:118px;border-top-left-radius:10px;border-top-right-radius:10px;border-bottom:1px solid #dddddd;background:url(../images/qq-icon-bg.png) no-repeat center 8px;}
        .main-im .img-qq{max-width:60px;display:block;position:absolute;left:6px;top:3px;-moz-transition:all 0.5s;-webkit-transition:all 0.5s;-o-transition:all 0.5s;transition:all 0.5s;}
        .main-im .im-qq:hover .img-qq{max-width:70px;left:1px;top:8px;position:absolute;}
        .main-im .im_main{background:#F9FAFB;border:1px solid #dddddd;border-radius:10px;background:#F9FAFB;display:none;}
        .main-im .im_main .im-tel{color:#000000;text-align:center;width:109px;height:160px;border-bottom:1px solid #dddddd;}
        .main-im .im_main .im-tel div{font-weight:bold;font-size:12px;margin-top:6px;}
        .main-im .im_main .im-tel:hover{background:#fafafa;}
        .main-im .im_main .go-top a{display:block;width:52px;height:80px;}
    </style>
    <script src="/js/pf.js"></script>

    <title>错误章节</title>
    <div class="bg-white">
        @if($chapters && $article)
            <div class="text-left">
                <h2>{{ $article->articlename }}</h2>
            </div>
            {{ $chapters->links() }}
            <ul role="list" class="divide-y divide-gray-100">
                @foreach ($chapters as $chapter)

                    @if(!$chapter->error_message && !request('only_error'))
                        @continue
                    @endif
                    <li class="flex justify-between gap-x-6 py-5">
                        <div tabindex="0"
                             class="collapse collapse-plus border border-base-300 bg-base-100 rounded-box ">
                            <div class="collapse-title text-xl font-medium">
                                <div class="flex gap-x-4">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-sm font-semibold leading-6 text-gray-900">
                                            <kbd class="kbd">{{ $chapter->chapterorder -1 }}</kbd>
                                            ({{ $chapter->chapterid }})
                                            {{ $chapter->chaptername }}
                                        </p>
                                        <p class="mt-1 truncate text-sm leading-5 text-gray-500">
                                            <?php echo date('Y-m-d H:i:s', $chapter->lastupdate); ?>
                                        </p>
                                        <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                                            {{ $chapter->size }}

                                        </p>
                                        <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                                            @if($chapter->error_message)
                                                @foreach ($chapter->error_message as $message)
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">
                                             {{$message}}
                                            </span>
                                                @endforeach
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse-content">
                                {{$chapter->content}}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            {{ $chapters->links() }}
        @endif

            <div class="main-im">
                <div class="im_main" id="im_main">
                    <div class="im-tel">
                        <div><a href="{{route('create-source', $article->articleid)}}" target="_blank">source</a></div>

                    @foreach($source_articles as $source_article)
                            <div>
                                <?php
                                    if($source_article->source == 'mayi'){
                                        $source_article->origin_url = str_replace('m.', 'www.', $source_article->origin_url);
                                    }
                                    if($source_article->source == 'biqu789'){
                                        $source_article->origin_url = str_replace('com', 'net', $source_article->origin_url);
                                    }
                                ?>
                                <a target="_blank" href="{{$source_article->origin_url}}">{{$source_article->source}}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

    </div>

@endsection
