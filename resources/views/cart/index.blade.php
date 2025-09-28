<x-layout>
    <h1 class="text-2xl font-bold mb-6">My Wishlist</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(count($cart))
        <ul class="space-y-4">
            @foreach($cart as $id => $job)
                <li class="border p-4 rounded flex justify-between">
                    <div>
                        <h2 class="font-semibold">{{ $job['title'] }}</h2>
                        <p>Salary: {{ $job['salary'] }}</p>
                        <p>Employer: {{ $job['employer'] }}</p>
                    </div>
                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                        @csrf
                        <x-forms.button type="submit" class="hover:bg-red-800 text-white px-4 py-2 rounded">
                            Remove
                        </x-forms.button>
                    </form>
                </li>
            @endforeach
        </ul>
    @else
        <p>Your wishlist is empty.</p>
    @endif
</x-layout>
