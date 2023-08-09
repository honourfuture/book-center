@extends('layouts.app')

@section('content')
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

    </div>

@endsection
