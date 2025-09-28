@props(['job'])

<x-panel class="flex flex-col text-center">
    <div class="text-sm flex justify-between">{{ $job->employer->name }}
        <form action="{{ route('cart.add', $job) }}" method="POST" class="mt-2">
            @csrf
            <x-forms.hover type="submit" class="text-white font-bold text-xs rounded-full">
                Wishlist Job
            </x-forms.hover>
        </form>
    </div>

    <div class="py-8">
        <h3 class="group-hover:text-blue-800 text-xl font-bold transition-colors duration-300">
            <a href="{{ $job->url }}" target="_blank">
                {{ $job->title }}
            </a>
        </h3>
        <p class="text-sm mt-4">{{ $job->salary }}</p>
    </div>

    <div class="flex justify-between items-center mt-auto">
        <div>
            @foreach($job->tags as $tag)
                <x-tag :$tag size="small" />
            @endforeach
        </div>

        <x-employer-logo :employer="$job->employer" :width="42"></x-employer-logo>
    </div>
</x-panel>