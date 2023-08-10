@extends('layouts.app')

@section('content')

    <style>
        .arrow-up:before {
            content: "\25B2"; /* 向上箭头符号 */
        }

        .arrow-down:before {
            content: "\25BC"; /* 向下箭头符号 */
        }

        .red {
            color: red;
        }

        .green {
            color: green;
        }
    </style>
    <title>蜘蛛统计</title>

    <div class="overflow-x-auto">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="/spider_statics">蜘蛛统计</a></li>
            </ul>
        </div>
        <table class="table table-xs w-full">
            <thead>
            <tr>
                <th>日期</th>
                @foreach($sources as $source)
                    <th>{{ $source }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($data as $date => $sources)
                <tr>
                    <td>{{ $date }}</td>
                    @foreach($sources as $source => $item)
                        <td>{{ $item['total'] }} <span class="{{ $item['arrowClass'] }}"></span></td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>

@endsection
