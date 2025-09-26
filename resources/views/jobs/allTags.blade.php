<x-layout>
    <x-page-heading>List of all Tags</x-page-heading>
    <div class="space-y-10">

            <div class="mt-6 flex flex-wrap gap-3">
                @foreach($tags as $tag)
                <x-tag :$tag></x-tag>
                @endforeach
            </div>

    </div>

</x-layout>