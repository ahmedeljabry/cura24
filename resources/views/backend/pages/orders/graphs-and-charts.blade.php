@extends('backend.admin-master')
@section('site-title')
    {{__('Graphs and Charts')}}
@endsection

@section('style')
    <x-datatable.css/>
@endsection

@section('content')
    <div class="row mt-5">
    <div class="col-md-6">
        <div class="card-wrapper">
            <div class="line-charts-wrapper">
                <div class="line-top-contents">
                    <h5 class="earning-title">{{ __('Monthly Income Overview') }}</h5>
                </div>
                <div class="line-charts">
                    <canvas id="line-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-wrapper">
            <div class="line-charts-wrapper">
                <div class="line-top-contents">
                    <h5 class="earning-title">{{ __('Daily Income Overview (Last 30 Days)') }}</h5>
                </div>
                <div class="line-charts">
                    <canvas id="line-chart2"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-6">
        <div class="card-wrapper">
            <div class="line-charts-wrapper">
                <div class="line-top-contents">
                    <h5 class="earning-title">{{ __('Monthly Order Overview') }}</h5>
                </div>
                <div class="line-charts">
                    <canvas id="line-chart3"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-wrapper">
            <div class="line-charts-wrapper">
                <div class="line-top-contents">
                    <h5 class="earning-title">{{ __('Daily Order Overview (Last 30 Days)') }}</h5>
                </div>
                <div class="line-charts">
                    <canvas id="line-chart4"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-wrapper">
            <div class="line-charts-wrapper">
                <div class="line-top-contents">
                    <h5 class="earning-title">{{ __('Monthly Failed Orders') }}</h5>
                </div>
                <div class="line-charts">
                    <canvas id="line-chart5"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-wrapper">
            <div class="line-charts-wrapper">
                <div class="line-top-contents">
                    <h5 class="earning-title">{{ __('Daily Failed Orders Last 30 Days') }}</h5>
                </div>
                <div class="line-charts">
                    <canvas id="line-chart6"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('script')
    <script src="{{asset('assets/backend/js/chart.js')}}"></script>
    <script>
        // Line Chart - Monthly Income
        new Chart(document.getElementById("line-chart"), {
            type: 'line',
            data: {
                labels: [@foreach($month_list as $month) "{{ $month }}", @endforeach],
                datasets: [{
                    label: "Monthly Income",
                    data: [@foreach($monthly_income_list as $amount) "{{ $amount }}", @endforeach],
                    borderColor: "#1DBF73",
                    borderWidth: 3,
                    fill: false,
                    pointBackgroundColor: '#fff',
                    pointHoverBackgroundColor: "#1DBF73",
                    pointRadius: 5,
                }]
            }
        });

        // Bar Chart - Daily Income
        new Chart(document.getElementById("line-chart2"), {
            type: 'bar',
            data: {
                labels: [@foreach($days_list as $day) "{{ $day }}", @endforeach],
                datasets: [{
                    label: "Daily Income",
                    data: [@foreach($daily_income_list as $amount) "{{ $amount }}", @endforeach],
                    backgroundColor: "#D9E268",
                    borderWidth: 2
                }]
            }
        });

        // Line Chart - Monthly Orders
        new Chart(document.getElementById("line-chart3"), {
            type: 'line',
            data: {
                labels: [@foreach($month_list as $month) "{{ $month }}", @endforeach],
                datasets: [{
                    label: "Monthly Orders",
                    data: [@foreach($monthly_order_list as $count) "{{ $count }}", @endforeach],
                    borderColor: "#2F98DC",
                    borderWidth: 3,
                    fill: false,
                    pointBackgroundColor: '#fff',
                    pointHoverBackgroundColor: "#2F98DC",
                    pointRadius: 5,
                }]
            }
        });

        // Bar Chart - Daily Orders
        new Chart(document.getElementById("line-chart4"), {
            type: 'bar',
            data: {
                labels: [@foreach($days_list as $day) "{{ $day }}", @endforeach],
                datasets: [{
                    label: "Daily Orders",
                    data: [@foreach($daily_order_list as $count) "{{ $count }}", @endforeach],
                    backgroundColor: "#ED27AB",
                    borderWidth: 2
                }]
            }
        });

        new Chart(document.getElementById("line-chart5"), {
            type: 'line',
            data: {
                labels: [@foreach($month_list as $list) "{{ $list }}", @endforeach],
                datasets: [{
                    data: [@foreach($monthly_failed_order_list as $list) "{{ $list }}", @endforeach],
                    label: "Monthly Failed Orders",
                    borderColor: "#FF4C4C",
                    borderWidth: 3,
                    fill: false,
                    pointBorderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "#FF4C4C",
                }]
            },
        });

        // Daily Failed Orders
        new Chart(document.getElementById("line-chart6"), {
            type: 'bar',
            data: {
                labels: [@foreach($days_list as $list) "{{ $list }}", @endforeach],
                datasets: [{
                    data: [@foreach($daily_failed_order_list as $list) "{{ $list }}", @endforeach],
                    label: "Daily Failed Orders",
                    backgroundColor: "#ED7D31",
                    borderColor: "#ED7D31",
                    borderWidth: 1
                }]
            },
        });
    </script>
@endsection
