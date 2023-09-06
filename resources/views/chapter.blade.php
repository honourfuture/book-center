@extends('layouts.app')

@section('content')

    <style>
        /*reset*/
        *{margin:0;padding:0;list-style-type:none;}
        a,img{border:0;}
        a{text-decoration:none;}
        a:hover{text-decoration:underline;}
        /*main css*/
        .main-im{position:fixed;right:10px;top:300px;z-index:100;width:110px;height:272px;}
        .main-im .qq-a{display:block;width:106px;height:116px;font-size:14px;color:#0484cd;text-align:center;position:relative;}
        .main-im .qq-a span{bottom:5px;position:absolute;width:90px;left:10px;}
        .main-im .qq-hover-c{width:70px;height:70px;border-radius:35px;position:absolute;left:18px;top:10px;overflow:hidden;z-index:9;}
        .main-im .qq-container{z-index:99;position:absolute;width:109px;height:118px;border-top-left-radius:10px;border-top-right-radius:10px;border-bottom:1px solid #dddddd;background:url(../images/qq-icon-bg.png) no-repeat center 8px;}
        .main-im .img-qq{max-width:60px;display:block;position:absolute;left:6px;top:3px;-moz-transition:all 0.5s;-webkit-transition:all 0.5s;-o-transition:all 0.5s;transition:all 0.5s;}
        .main-im .im-qq:hover .img-qq{max-width:70px;left:1px;top:8px;position:absolute;}
        .main-im .im_main{background:#F9FAFB;border:1px solid #dddddd;border-radius:10px;background:#F9FAFB;display:none;}
        .main-im .im_main .im-tel{color:#000000;text-align:center;width:109px;height:105px;border-bottom:1px solid #dddddd;}
        .main-im .im_main .im-tel div{font-weight:bold;font-size:12px;margin-top:6px;}
        .main-im .im_main .im-tel .tel-num{font-family:Arial;font-weight:bold;color:#e66d15;}
        .main-im .im_main .im-tel:hover{background:#fafafa;}
        .main-im .im_main .weixing-container{width:55px;height:47px;border-right:1px solid #dddddd;background:#f5f5f5;border-bottom-left-radius:10px;background:url(../images/weixing-icon.png) no-repeat center center;float:left;}
        .main-im .im_main .weixing-show{width:112px;height:172px;background:#ffffff;border-radius:10px;border:1px solid #dddddd;position:absolute;left:-125px;top:-126px;}
        .main-im .im_main .weixing-show .weixing-sanjiao{width:0;height:0;border-style:solid;border-color:transparent transparent transparent #ffffff;border-width:6px;left:112px;top:134px;position:absolute;z-index:2;}
        .main-im .im_main .weixing-show .weixing-sanjiao-big{width:0;height:0;border-style:solid;border-color:transparent transparent transparent #dddddd;border-width:8px;left:112px;top:132px;position:absolute;}
        .main-im .im_main .weixing-show .weixing-ma{width:104px;height:103px;padding-left:5px;padding-top:5px;}
        .main-im .im_main .weixing-show .weixing-txt{position:absolute;top:110px;left:7px;width:100px;margin:0 auto;text-align:center;}
        .main-im .im_main .go-top a{display:block;width:52px;height:47px;}
        .main-im .close-im{position:absolute;right:10px;top:-12px;z-index:100;width:24px;height:24px;}
        .main-im .close-im a:hover{text-decoration:none;}
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
