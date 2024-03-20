@extends('layouts.app')
<link type="text/css" rel="stylesheet" href="/js/jsDate/skin/jedate.css">
<script type="text/javascript" src="/js/jsDate/src/jedate.js"></script>

@section('content')
    <title>log 日志</title>
    <div class="bg-white">
        <form action="/do-create-sources" method="post">
            <div class="space-y-12">
                <div class="border-b border-gray-900/10 pb-12">
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <select class="select select-bordered w-full max-w-xs">
                            <option>Han Solo</option>
                            <option>Greedo</option>
                        </select>
                    </div>

                    <div class="border-b border-gray-900/10 pb-12 pt-2">
                        <div class="jeinpbox">
                            <input type="text" class="input input-bordered w-full max-w-xs" id="test03" placeholder="YYYY-MM-DD">
                        </div>
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
    <script>
        jeDate("#test03",{
            //onClose:false,
            format: "YYYY-MM-DD"
        });
    </script>

@endsection
