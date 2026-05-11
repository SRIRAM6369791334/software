@if(session('success'))
    <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 shadow-sm">
        <span class="material-symbols-rounded text-xl">check_circle</span>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-5 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 shadow-sm">
        <span class="material-symbols-rounded text-xl">error</span>
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
        <p class="mb-1 flex items-center gap-2 font-bold">
            <span class="material-symbols-rounded text-xl">warning</span>
            Please fix the following errors:
        </p>
        <ul class="list-inside list-disc space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
