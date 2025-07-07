<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Currencies</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($currencies as $currency)
                <a href="{{ route('currencies.show', ['PLN', $currency->symbol]) }}" class="block bg-gray-50 p-4 rounded-lg border hover:bg-gray-100 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-semibold text-blue-600">PLN to {{ $currency->symbol }}</span>
                        <span class="text-gray-600">{{ $currency->name }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
