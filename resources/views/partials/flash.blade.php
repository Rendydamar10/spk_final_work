@if(session('success'))
    <div class="mb-4 p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 rounded-xl bg-red-50 text-red-700 border border-red-200">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-4 rounded-xl bg-red-50 text-red-700 border border-red-200">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
