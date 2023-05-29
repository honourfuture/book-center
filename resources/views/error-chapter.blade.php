@extends('layouts.app')

@section('content')
    <div class="bg-white">
        <div class="text-left">
        </div>
        <ul role="list" class="divide-y divide-gray-100">
            @foreach($articles as $article)
                <li class="flex justify-between gap-x-6 py-5">
                    <div class="flex gap-x-4">
                        <div class="min-w-0 flex-auto">
                            <a href="https://m.juzhishuwu.com/xs/{{$article->articleid}}.html" target="_blank">
                                <p class="text-sm font-semibold leading-6 text-gray-900">{{ $article->articlename }}</p>
                            </a>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500"><?php echo date('Y-m-d H:i:s', $article->lastupdate )?></p>
                        </div>
                    </div>
                    <div style="width: 70%">
                        <div tabindex="0" class="collapse collapse-plus border border-base-300 bg-base-100 rounded-box">
                            <div class="collapse-title text-xl  ">
                                异常章节详情 <?php echo $article->error_chapters->count();?>
                            </div>
                            <div class="collapse-content">
                                <div class="space-y-5">
                                    @foreach($article->error_chapters as $error_chapter)
                                        <div class="p-3 bg-white shadow rounded-lg">
                                            <h3 class="border-b text-sm font-semibold leading-6 text-gray-900 pt-2">
                                                <a class="text-red-600	" href="https://m.juzhishuwu.com/0_{{$article->articleid}}/{{$error_chapter->chapterid}}.html" target="_blank">
                                                    {{ $error_chapter->chapterorder }}
                                                </a>
                                                {{ $error_chapter->chaptername }}
                                                <?php echo date('Y-m-d H:i:s', $article->lastupdate )?>
                                            </h3>
                                            <p class="text-sm font-semibold leading-6 text-gray-900 pt-2">
                                                {{ $error_chapter->error_message }}
                                            </p>
                                            <p class="text-sm font-semibold leading-6 text-gray-900 pt-2 ">
                                                <xmp><?php echo mb_substr($error_chapter->content, 0, 150)?></xmp>
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="hidden sm:flex sm:flex-col sm:items-end">
                        <p class="text-sm leading-6 text-gray-900">{{ $article->author }} / {{ $article->sortid }}</p>
                    </div>
                </li>
            @endforeach
        </ul>


    </div>

@endsection
