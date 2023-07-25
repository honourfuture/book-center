@extends('layouts.app')

@section('content')

    <form>
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <div class="col-span-full">
                    <label for="about" class="block text-sm font-medium leading-6 text-gray-900">IDS - 1</label>
                    <div class="mt-2">
                        <textarea id="ids-1" name="ids_1" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                    </div>
                </div>

                <div class="col-span-full">
                    <label for="about" class="block text-sm font-medium leading-6 text-gray-900">IDS - 2</label>
                    <div class="mt-2">
                        <textarea id="ids-2" name="ids_2" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                    </div>
                </div>

                <div class="col-span-full">
                    <label for="about" class="block text-sm font-medium leading-6 text-gray-900">IDS - 3</label>
                    <div class="mt-2">
                        <textarea id="ids-3" name="ids_3" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                    </div>
                </div>
            </div>


            <div class="mt-6 flex items-center justify-end gap-x-6">
                <button type="button"
                        id="calc"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Diff
                </button>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $('#calc').click(function () {
            let ids_1 = $('#ids-1').val()
            let ids_2 = $('#ids-2').val()

            let ids_3 = findDifference(ids_1, ids_2)

            $("#ids-3").val(ids_3)

        })


        function findDifference(str1, str2) {
            var arr1 = str1.split(',');
            var arr2 = str2.split(',');
            var diffArr = [];

            // 检查在arr1中存在但在arr2中不存在的数字
            // for (var i = 0; i < arr1.length; i++) {
            //     if (arr2.indexOf(arr1[i]) === -1) {
            //         diffArr.push(arr1[i]);
            //     }
            // }

            // // 检查在arr2中存在但在arr1中不存在的数字
            for (var j = 0; j < arr2.length; j++) {
                if (arr1.indexOf(arr2[j]) === -1) {
                    diffArr.push(arr2[j]);
                }
            }

            var diffStr = diffArr.join(',');
            return diffStr;
        }
    </script>
@endsection
