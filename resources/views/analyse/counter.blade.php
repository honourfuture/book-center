@extends('layouts.app')
<title>统计</title>

@section('content')
    <div class="text-sm breadcrumbs">
        <ul>
            <li>统计</li>
        </ul>
    </div>
    <div class="overflow-x-auto">
        <table class="table table-xs">
            <thead>
            <tr>
                <th>ID</th>
                <th>日期</th>
                <th>日更新</th>
                @foreach($rule_errors as $rule)
                    <th>{{$rule['rule'].'-'.$rule['exid_lang']}}</th>
                @endforeach
            </tr>
            </thead>
                @foreach($counters as $counter)
                <tbody>

                <td>{{$counter['id']}}</td>
                    <td>{{$counter['date']}}</td>
                    <td>{{$counter['day_update_counter']}}</td>
                    @foreach($rule_errors as $rule_key => $rule)

                        <th>
                            @if(isset($counter['rule_counters'][$rule_key]))

                                {{$counter['rule_counters'][$rule_key]['count']}}
                            @else
                                0
                            @endif

                        </th>
                    @endforeach
                </tbody>

            @endforeach
            <tfoot>
            </tfoot>
        </table>
    </div>

@endsection
