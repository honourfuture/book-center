@extends('layouts.app')

@section('content')
    <title>绑定来源</title>
    <div class="bg-white">
        <form action="/do-create-sources" method="post">
            <div class="space-y-12">
                <div class="border-b border-gray-900/10 pb-12">
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        @foreach($sources as $source => $ids)
                            <?php
                                $article_ids = implode(',', array_unique($ids));
                            ?>
                                <div class="sm:col-span-3">
                                    <label for="article_id" class="block text-sm font-medium leading-6 text-gray-900">{{$source}}- ({{count($ids)}})</label>
                                    <div class="mt-2">
                                        <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                            <textarea name="sources[{{$source}}]" class="textarea textarea-info w-full" placeholder=" {{$source}}">{{$article_ids}}</textarea>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                @csrf
                <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
            </div>
        </form>

    </div>

@endsection
