@if ($errors->any())
    <div {{ $attributes }}>
        <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md" role="alert">
        <div class="flex">
            <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M19.707 5.293c.391.391.391 1.023 0 1.414l-11 11c-.391.391-1.023.391-1.414 0l-5-5c-.391-.391-.391-1.023 0-1.414s1.023-.391 1.414 0l4.293 4.293 10.293-10.293c.391-.391 1.023-.391 1.414 0z"/></svg></div>
            <div>
                <p class="font-bold">{{ __('general.success')}}</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

