<img src="{{ file_exists(public_path('build/assets/travelink.png')) ? asset('build/assets/travelink.png') : asset('images/travelink.png') }}" alt="{{ config('app.name', 'Travelink') }}" {{ $attributes->merge(['class' => 'h-9 w-auto object-contain']) }} />

