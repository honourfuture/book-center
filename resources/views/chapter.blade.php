<x-guest-layout>
    <div class="container">
        @foreach ($chapters as $chapter)
            {{ $chapter->name }}
        @endforeach
    </div>

{{ $chapters->links() }}
</x-guest-layout>
