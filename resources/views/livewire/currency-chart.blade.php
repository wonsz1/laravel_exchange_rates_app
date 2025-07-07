<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ $fromCurrency->symbol }} to {{ $toCurrency->symbol }} Exchange Rate</h2>
            <a href="{{ route('currencies.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to currencies</a>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg">
            <div id="chart" class="h-[400px]"></div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var options = {
        series: [{
            name: '{{ $fromCurrency->symbol }} to {{ $toCurrency->symbol }} Rate',
            data: @js($rates)
        }],
        chart: {
            id: "chart",
            type: 'line',
            height: 400,
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: @js($dates),
            title: {
                text: 'Date'
            }
        },
        yaxis: {
            title: {
                text: 'Exchange Rate'
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return value ? value.toFixed(4) : 'No data';
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>

