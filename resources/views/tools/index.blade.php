@extends('layouts.app')
<link type="text/css" rel="stylesheet" href="/js/jsDate/skin/jedate.css">
<script type="text/javascript" src="/js/jsDate/src/jedate.js"></script>

@section('content')
    <title>log 日志</title>
    <div class="bg-white">
        <form id="log-form"> <!-- Added an ID to the form for easier reference -->
            <div class="space-y-12">
                <div class="border-gray-900/10 pb-12">
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-5">
                        <select class="select select-bordered w-full max-w-xs" name="log_name">
                            @foreach($logs as $log)
                                <option value="{{$log['access_log']}}">{{$log['access_log']}}</option>
                            @endforeach
                        </select>

                        <div class="jeinpbox">
                            <input type="text" class="input input-bordered w-full max-w-xs" name="log_date" id="log-date" placeholder="YYYY-MM-DD" value="{{date('Y-m-d')}}">
                        </div>

                        <select class="select select-bordered w-full max-w-xs" name="spider">
                            @foreach($spiders as $key => $spider)
                                <option value="{{$spider}}">{{$key}}</option>
                            @endforeach
                        </select>
                        <select class="select select-bordered w-full max-w-xs" name="log_type">
                            @foreach($logTypes as $key => $logType)
                                <option value="{{$key}}">{{$key}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class=" border-gray-900/10 pb-4 pt-4">
                        <div class="mockup-code">
                            <pre data-prefix="$"><code id="code">?</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                @csrf
                <button type="button" id="cancel-btn" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                <button type="button" id="save-btn" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
            </div>
        </form>
    </div>
    <script>
        jeDate("#log-date",{
            //onClose:false,
            theme:{ bgcolor:"#4a00ff",color:"#ffffff", pnColor:"#d1dbff"},
            format: "YYYY-MM-DD"
        });

        // AJAX Submit with jQuery
        $('#save-btn').click(function() {
            var formData = $('#log-form').serialize(); // Serialize form data

            // Send AJAX request
            $.ajax({
                url: '/do-log-shell',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if(response.code == 200){
                        $('#code').html(response.data.shell)
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error('Error:', error);
                }
            });
        });

    </script>
@endsection
