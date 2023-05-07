@extends('layouts.app')

@section('content')

    <form>
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="start-chapter-id" class="block text-sm font-medium leading-6 text-gray-900">local
                            start chapter id</label>
                        <div class="mt-2">
                            <input type="text" name="start-chapter-id" id="start-chapter-id" autocomplete="given-name"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-4">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="end-chapter-id" class="block text-sm font-medium leading-6 text-gray-900">local end
                            chapter id</label>
                        <div class="mt-2">
                            <input type="text" name="end-chapter-id" id="end-chapter-id" autocomplete="family-name"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-4">
                        </div>
                    </div>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="origin-start-chapter-id" class="block text-sm font-medium leading-6 text-gray-900">origin
                            start chapter id</label>
                        <div class="mt-2">
                            <input type="text" name="origin-start-chapter-id" id="origin-start-chapter-id"
                                   autocomplete="given-name"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-4">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="origin-end-chapter-id" class="block text-sm font-medium leading-6 text-gray-900">origin
                            end chapter id</label>
                        <div class="mt-2">
                            <input type="text" name="origin-end-chapter-id" id="origin-end-chapter-id"
                                   autocomplete="family-name"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-4">
                        </div>
                    </div>
                </div>
            </div>


            <div class="mt-6 flex items-center justify-end gap-x-6">
                <button type="button"
                        id="calc"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Calc
                </button>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $('#calc').click(function () {
            let start_chapter_id = parseInt($('#start-chapter-id').val())
            let end_chapter_id = parseInt($('#end-chapter-id').val())
            let origin_start_chapter_id = parseInt($('#origin-start-chapter-id').val())

            let end_start_chapter_id = end_chapter_id - start_chapter_id + origin_start_chapter_id;

            $("#origin-end-chapter-id").val(end_start_chapter_id);
        })
    </script>
@endsection
