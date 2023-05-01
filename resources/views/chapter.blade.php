<!doctype html>
<head>
    <!-- ... --->
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.6/dist/full.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet" type="text/css" />
</head>
<div class="bg-white px-4 py-12 sm:px-6 lg:px-8">
    <div class="text-left">
        <h2>{{ $article->articlename }}</h2>
    </div>
    {{ $chapters->links() }}
    <ul role="list" class="divide-y divide-gray-100">
        @foreach ($chapters as $chapter)
            <li class="flex justify-between gap-x-6 py-5">
                <div class="flex gap-x-4">
                    <div class="min-w-0 flex-auto">
                        <p class="text-sm font-semibold leading-6 text-gray-900">({{ $chapter->chapterid }}
                            ) {{ $chapter->chaptername }}</p>
                        <p class="mt-1 truncate text-xs leading-5 text-gray-500">
                            {{ $chapter->size }}
                            @if($chapter->exists)
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                    未找到文件
                                </span>
                            @endif

                            @if(!$chapter->exists && $chapter->error_message)
                                @foreach ($chapter->error_message as $message)
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">
                                {{$message}}
                                </span>
                                @endforeach
                            @endif
                        </p>
                    </div>
                </div>
                <div tabindex="0" class="collapse collapse-plus border border-base-300 bg-base-100 rounded-box w-1/2">
                    <div class="collapse-title text-xl font-medium">
                        章节内容
                    </div>
                    <div class="collapse-content">
                        {{$chapter->content}}
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
    {{ $chapters->links() }}

</div>

