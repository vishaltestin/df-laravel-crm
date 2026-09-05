@extends('FrontEnd.layouts.master')
@section('title', 'Dashboards')
@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1')
            Dashboards
        @endslot
        @slot('title')
            CRM
        @endslot
    @endcomponent
@php
$totalPaidSafe = $totalPaidCount > 0 ? $totalPaidCount : 1;

$paidOnlinePercent  = round(($paidOnlineCount  / $totalPaidSafe) * 100);
$paidOfflinePercent = round(($paidOfflineCount / $totalPaidSafe) * 100);
$paidCashPercent    = round(($paidCashCount    / $totalPaidSafe) * 100);
$paidDDPercent      = round(($paidDDCount      / $totalPaidSafe) * 100);
$paidFreePercent    = round(($paidFreeCount    / $totalPaidSafe) * 100);
@endphp



   <div class="row">
        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-body p-0">
                    <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0">
                        <div class="col">
                         <div class="py-4 px-3">
    <h5 class="text-muted text-uppercase fs-13">
        Total Leads
        <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
    </h5>

    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            <i class="ri-space-ship-line display-6 text-muted"></i>
        </div>

        <div class="flex-grow-1 ms-3">
            <h2 class="mb-0">
                <span class="counter-value" data-target="{{ $total }}">
                    {{ $total }}
                </span>
            </h2>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-evenly">
    <p class="text-muted mb-0">
        <i class="mdi mdi-numeric-0-circle text-success fs-18 align-middle me-2"></i>
        Verified: <strong>{{ $verified }}</strong>
    </p>

    <p class="text-muted mb-0">
        <i class="mdi mdi-numeric-0-circle text-info fs-18 align-middle me-2"></i>
        Unverified: <strong>{{ $unverified }}</strong>
    </p>
</div>

<div class="progress animated-progress rounded-bottom rounded-0" style="height: 6px;">
    <div class="progress-bar bg-success rounded-0"
         role="progressbar"
         style="width: {{ $verifiedPercent }}%"
         aria-valuenow="{{ $verifiedPercent }}"
         aria-valuemin="0"
         aria-valuemax="100">
    </div>

    <div class="progress-bar bg-info rounded-0"
         role="progressbar"
         style="width: {{ $unverifiedPercent }}%"
         aria-valuenow="{{ $unverifiedPercent }}"
         aria-valuemin="0"
         aria-valuemax="100">
    </div>
</div>

				
                        </div><!-- end col -->
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Paid Applications <i
                                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0">
  <span class="counter-value" data-target="{{ $totalPaidCount }}">
    {{ $totalPaidCount }}
  </span>
</h2>
<small class="text-muted">Total Paid Applications</small>     </div>
                                </div>
 
                            </div>
							
<div class="d-flex flex-wrap justify-content-evenly">
  <p class="text-muted mb-0">Online: {{ $paidOnlineCount }}</p>
  <p class="text-muted mb-0">Offline: {{ $paidOfflineCount }}</p>
  <p class="text-muted mb-0">Cash: {{ $paidCashCount }}</p>
  <p class="text-muted mb-0">DD: {{ $paidDDCount }}</p>
  <p class="text-muted mb-0">Free: {{ $paidFreeCount }}</p>
</div>


<div class="progress animated-progress rounded-bottom rounded-0" style="height:6px;">
  <div class="progress-bar bg-success" style="width:{{ $paidOnlinePercent }}%"></div>
  <div class="progress-bar bg-info" style="width:{{ $paidOfflinePercent }}%"></div>
  <div class="progress-bar bg-primary" style="width:{{ $paidCashPercent }}%"></div>
  <div class="progress-bar bg-warning" style="width:{{ $paidDDPercent }}%"></div>
  <div class="progress-bar bg-danger" style="width:{{ $paidFreePercent }}%"></div>
</div>


							
							
                        </div><!-- end col -->
						
						
						    <div class="col">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Daily Average Income <i
                                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-trophy-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0">
											  <span class="counter-value" data-target="{{ round($todayRevenue) }}">
											   {{ round($todayRevenue) }}
											 </span>
											</h2>

                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                    
						
						
						
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Lead Conversation <small>(Inrollments)</small> <i
                                        class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i></h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-pulse-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $leadConversion }}">   {{ $leadConversion }}</span>%</h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
						
						
						  <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">UnPaid Applications <i
                                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                       <h2 class="mb-0">
										  <span class="counter-value" data-target="{{ $unpaidApplications }}">
										   {{ $unpaidApplications }}
										 </span>
										</h2>

                                    </div>
                                </div>
                            </div> 
						@php
$processed = round($unpaidApplications * 0.6);
$unapproved = $unpaidApplications - $processed;
@endphp
	
					<div class="d-flex flex-wrap justify-content-evenly">
                        <p class="text-muted mb-0"><a href=""><i class="mdi mdi text-success fs-18 align-middle me-2"></i> {{ $processed }} Processed</a></p>
                        <p class="text-muted mb-0"><a href=""><i class="mdi mdi text-info fs-18 align-middle me-2"></i>{{ $unapproved }} Unapproved</a></p>
                     </div>


<div class="progress animated-progress rounded-bottom rounded-0" style="height: 6px;">
                    <div class="progress-bar bg-success rounded-0" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    <div class="progress-bar bg-info rounded-0" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                 </div>
				 
                        </div><!-- end col -->
                        <div class="col">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Pending Followups <i
                                        class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i></h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-service-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                       <h2 class="mb-0">
												 <span class="counter-value" data-target="{{ $pendingFollowups }}">
												   {{ $pendingFollowups }}
												 </span>
												</h2>

                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                    </div><!-- end row -->
					
					
					
					
					
			<div class="row">
  <div class="col-md-3">
    <div class="card p-3">
      <h6>Total Tickets</h6>
      <h3>{{ $totalQueries }}</h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3">
      <h6>Open</h6>
      <h3>{{ $openQueries }}</h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3">
      <h6>In Progress</h6>
      <h3>{{ $inProgressQueries }}</h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3">
      <h6>Closed</h6>
      <h3>{{ $closedQueries }}</h3>
    </div>
  </div>
</div>
		
					
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->

    <div class="row">
        <div class="col-xxl-6 col-md-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Sales Forecast</h4>
                    <div class="flex-shrink-0">
              
                    </div>
                </div><!-- end card header -->
           <div class="card-body px-0">
    <div id="revenue-expenses-charts"
         class="apex-charts"
         style="min-height:320px"></div>
</div>

            </div><!-- end card -->
        </div><!-- end col -->

        <div class="col-xxl-3 col-md-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Deal Type</h4>
                    <div class="flex-shrink-0">
                      
                    </div>
                </div><!-- end card header -->
                <div class="card-body pb-0">
                    <div id="deal-type-charts" data-colors='["--vz-warning", "--vz-danger", "--vz-success"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
     <div class="col-xxl-3 col-md-6">
	 <div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Admission Funnel</h4>
    </div>
    <div class="card-body">
        <div id="funnel-chart"></div>
    </div>
</div>
</div>
     <div class="col-xxl-3 col-md-6">


<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Team Performance</h4>
    </div>
    <div class="card-body">
        <div id="team-performance-chart"></div>
    </div>
</div>

</div>
 <div class="col-xxl-3 col-md-6">
<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Lead Source Performance</h4>
    </div>
    <div class="card-body">
        <div id="source-chart"></div>
    </div>
</div>
</div>

 <div class="col-xxl-3 col-md-6">
<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Lead Source Performance</h4>
    </div>
    <div class="card-body">
        <div id="source-chart-p"></div>
    </div>
</div>
</div>


        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Balance Overview</h4>
                   
                </div><!-- end card header -->
                <div class="card-body px-0">
                    <ul class="list-inline main-chart text-center mb-0">
    <li class="list-inline-item chart-border-left me-0 border-0">
        <h4 class="text-primary">
            ₹{{ number_format($monthRevenue) }}
            <span class="text-muted d-inline-block fs-14 align-middle ms-2">
                Revenue (This Month)
            </span>
        </h4>
    </li>

    <li class="list-inline-item chart-border-left me-0">
        <h4>
            ₹0
            <span class="text-muted d-inline-block fs-14 align-middle ms-2">
                Expenses
            </span>
        </h4>
    </li>

    <li class="list-inline-item chart-border-left me-0">
        <h4>
            {{ $monthRevenue > 0 ? '100' : '0' }}%
            <span class="text-muted d-inline-block fs-14 align-middle ms-2">
                Profit Ratio
            </span>
        </h4>
    </li>
</ul>

                    <div id="revenue-expenses-charts" data-colors='["--vz-success", "--vz-danger"]' class="apex-charts"
                        dir="ltr"></div>
                </div>
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->
 <script>
$(function() {
   // alert('Hello from jQuery');
});
</script>

@endsection
@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
     <script>
 if (document.querySelector("#source-chart-p")) {

    var options = {
        series: [{
            name: 'Leads',
            data: {!! json_encode($sourceData) !!}
        }],

        chart: {
            type: 'bar',
            height: 320,
            toolbar: { show: false }
        },

        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '45%',
                endingShape: 'rounded'
            }
        },

        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val;
            },
            style: {
                fontSize: '12px',
                fontWeight: 'bold'
            }
        },

        xaxis: {
            categories: {!! json_encode($sourceLabels) !!},
            title: {
                text: 'Lead Source'
            }
        },

        yaxis: {
            title: {
                text: 'Total Leads'
            }
        },

        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " Leads";
                }
            }
        }
    };

    new ApexCharts(
        document.querySelector("#source-chart-p"),
        options
    ).render();
}
 	 
	 if (document.querySelector("#source-chart")) {
    var options = {
        series: {!! json_encode($sourceData) !!},
        chart: {
            type: "donut",
            height: 300
        },
        labels: {!! json_encode($sourceLabels) !!},
        legend: {
            position: "bottom"
        }
    };

    new ApexCharts(
        document.querySelector("#source-chart"),
        options
    ).render();
}

	 if (document.querySelector("#revenue-expenses-charts")) {

    var options = {
        series: [
            {
                name: "Revenue",
                data: [
                    {{ $todayRevenue }},
                    {{ $monthRevenue }}
                ]
            }
        ],
        chart: {
            height: 300,
            type: "area",
            toolbar: { show: false }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: "smooth"
        },
        xaxis: {
            categories: ["Today", "This Month"]
        },
        colors: ["#28a745"],
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "₹ " + val;
                }
            }
        }
    };

    var chart = new ApexCharts(
        document.querySelector("#revenue-expenses-charts"),
        options
    );
    chart.render();
}

	 
	 
	 
	 
	 
	 
	 
	 document.addEventListener("DOMContentLoaded", function () {

    const el = document.querySelector("#deal-type-charts");
    if (!el) return;

    const d = window.dealTypeData || { paid: 0, unpaid: 0, free: 0 };

    const options = {
        chart: {
            type: "donut",
            height: 260
        },
        series: [d.paid, d.unpaid, d.free],
        labels: ["Paid Applications", "Unpaid Applications", "Free"],
        legend: {
            position: "bottom"
        },
        dataLabels: {
            enabled: true
        },
        tooltip: {
            y: {
                formatter: val => val + " Applications"
            }
        }
    };

    new ApexCharts(el, options).render();
});

$(function () {

  $.get("{{ route('dashboard.graphs') }}", function (res) {
    console.log("Dashboard Graph Data", res);

    // =========================
    // SALES FORECAST BAR
    // =========================
    new ApexCharts(document.querySelector("#sales-forecast-chart"), {
      chart: { type: 'bar', height: 280 },
      series: [{
        name: 'Revenue',
        data: res.sales_forecast
      }],
      xaxis: {
        categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
      }
    }).render();

    // =========================
    // DEAL TYPE DONUT
    // =========================
    new ApexCharts(document.querySelector("#deal-type-charts"), {
      chart: { type: 'donut', height: 280 },
      labels: ['Paid', 'Unpaid'],
      series: [res.deal_type.Paid, res.deal_type.Unpaid]
    }).render();

    // =========================
    // BALANCE OVERVIEW LINE
    // =========================
    new ApexCharts(document.querySelector("#revenue-expenses-charts"), {
      chart: { type: 'line', height: 300 },
      series: [
        { name: 'Revenue', data: res.revenue },
        { name: 'Expenses', data: res.expenses }
      ],
      xaxis: {
        categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
      }
    }).render();

  });

});
</script>
<script>
window.dealTypeData = {
    paid: {{ $dealPaid }},
    unpaid: {{ $dealUnpaid }},
    free: {{ $dealFree }}
};


if (document.querySelector("#funnel-chart")) {
    var options = {
        series: [
            {
                name: "Count",
                data: [
                    {{ $funnelLeads }},
                    {{ $funnelApplications }},
                    {{ $funnelPaid }},
                    {{ $funnelEnrolled }}
                ]
            }
        ],
        chart: {
            type: "bar",
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: "60%"
            }
        },
        xaxis: {
            categories: [
                "Total Leads",
                "Applications",
                "Paid",
                "Enrolled"
            ]
        },
        colors: ["#0ab39c"]
    };

    new ApexCharts(
        document.querySelector("#funnel-chart"),
        options
    ).render();
}
if (document.querySelector("#team-performance-chart")) {
    var options = {
        series: [
            {
                name: "Leads",
                data: {!! json_encode($teamLeads) !!}
            },
            {
                name: "Paid",
                data: {!! json_encode($teamPaid) !!}
            }
        ],
        chart: {
            type: "bar",
            height: 350
        },
        xaxis: {
            categories: {!! json_encode($teamLabels) !!}
        },
        colors: ["#405189", "#0ab39c"]
    };

    new ApexCharts(
        document.querySelector("#team-performance-chart"),
        options
    ).render();
}

</script>

@endsection
