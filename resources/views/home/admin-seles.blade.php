<div class="row mt-4">
    <!-- Chart Income -->
    <div class="col-lg-7 col-md-6 mb-4">
        <div class="card z-index-2">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0">Total Pemasukan</h6>
                    <p class="ps-3 m-0" style="font-size: 12px; font-weight: bold; color: #344767;" id="totalIncomeText">
                        Total Pemasukan: Rp {{ number_format(array_sum($adminSalesIncomeYear), 0, ',', '.') }}</p>
                </div>
                <div class="d-flex justify-content-between mt-2 mb-0 align-items-center">
                    <p class="text-sm m-0">
                        <i class="fa fa-arrow-up text-success"></i> Pendapatan berdasarkan pesanan Tahun
                    </p>

                    <select id="yearFilterAdmin" class="form-select form-select-sm w-auto"
                        style="border: 1px solid #d2d6da; padding-left: 10px; border-radius: 0.375rem;">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="chart">
                    <canvas id="incomeChart" class="chart-canvas" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Top Products -->
    <div class="col-lg-5 col-md-6 mb-4">
        <div class="card z-index-2">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6>Top 5 Produk</h6>
                    <p class="text-sm">Paling sering dipesan</p>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="chart">
                    <canvas id="topProductsChart" class="chart-canvas" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row ">
    <div class="col-12">
        <h6>Data Customer & Invoice</h6>

        <div class="card">
            <div class="card-body p-0">
                <div class="accordion" id="accordionCustomer">
                    @foreach($customersList as $index => $customer)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} p-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}" style="background-color: transparent; border: none; box-shadow: none;">
                                <div class="d-flex justify-content-between w-100 align-items-center">
                                    <span class="font-weight-bold mb-0" style="color: #344767;">{{ $customer->name }}</span>
                                    <span class="badge bg-gradient-success">Total Pemasukan: Rp {{ number_format($customer->invoices->sum('total_harga'), 0, ',', '.') }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionCustomer">
                            <div class="accordion-body p-3">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No Invoice</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Harga</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->invoices as $invoice)
                                            <tr>
                                                <td>
                                                    <h6 class="mb-0 text-sm ps-2">{{ $invoice->nomor_invoice }}</h6>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="font-weight-bold text-xs">Rp {{ number_format($invoice->total_harga, 0, ',', '.') }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="badge badge-sm bg-gradient-{{ $invoice->status == 'pending' ? 'warning' : 'success' }}">{{ ucfirst($invoice->status) }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-sm">Belum ada invoice</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof Chart !== 'undefined') {
            var ctx1 = document.getElementById("incomeChart").getContext("2d");

            var adminIncomeChart = new Chart(ctx1, {
                type: "line",
                data: {
                    labels: @json($months),
                    datasets: [{
                        label: "Income (Rp)",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#4CAF50",
                        backgroundColor: "rgba(76, 175, 80, 0.2)",
                        borderWidth: 3,
                        fill: true,
                        data: @json($adminSalesIncomeYear),
                        maxBarThickness: 6
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                padding: 10,
                                color: '#b2b9bf',
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                color: '#b2b9bf',
                                padding: 20,
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                            }
                        },
                    },
                },
            });

            document.getElementById('yearFilterAdmin').addEventListener('change', function () {
                var selectedYear = this.value;
                fetch('/fetch-admin-income-by-year?year=' + selectedYear)
                    .then(response => response.json())
                    .then(data => {
                        adminIncomeChart.data.datasets[0].data = data.income;
                        adminIncomeChart.update();

                        var totalFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data.total);
                        document.getElementById('totalIncomeText').innerText = 'Total Pemasukan: ' + totalFormatted;
                    })
                    .catch(error => console.error('Error fetching data:', error));
            });

            var ctx2 = document.getElementById("topProductsChart").getContext("2d");

            var topProductNames = @json($topProducts->pluck('product_name'));
            var topProductQuantities = @json($topProducts->pluck('total_quantity'));
            var topProductIncomes = @json($topProducts->pluck('total_income'));

            new Chart(ctx2, {
                type: "bar",
                data: {
                    labels: topProductNames,
                    datasets: [{
                        label: "Quantity Dipesan",
                        tension: 0.4,
                        borderWidth: 0,
                        borderRadius: 4,
                        borderSkipped: false,
                        backgroundColor: "#e91e63",
                        data: topProductQuantities,
                        maxBarThickness: 6
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                afterLabel: function (context) {
                                    let index = context.dataIndex;
                                    let income = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(topProductIncomes[index]);
                                    return 'Total Pemasukan: ' + income;
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                padding: 10,
                                color: '#b2b9bf',
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                color: '#b2b9bf',
                                padding: 20,
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                            }
                        },
                    },
                },
            });
        } else {
            console.error("Chart.js is not loaded.");
        }
    });
</script>