@extends('home')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow text-black">
    <h1 class="text-xl font-bold mb-4">Vizija - Pametni import podataka</h1>

    <a href="{{ asset('files\Vizija - Pametni import podataka.pdf') }}" 
       target="_blank" 
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
       Otvori PDF
    </a>

    {{-- Ako želiš prikaz unutar stranice --}}
    <div class="mt-6">
        <iframe src="{{ asset('files/Vizija - Pametni import podataka.pdf') }}" width="100%" height="600px"></iframe>
    </div>
</div>
@endsection