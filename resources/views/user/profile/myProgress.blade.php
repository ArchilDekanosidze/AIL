@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/profile/myProgress.css')}}">
@endsection
@section('content')
<div class="QuizResult main-body">
    <div class="chartHeader">
        <button class="parentBtn">بازگشت</button>
        <div class="parentCategoryName"></div>
    </div>    

    <div>
        <canvas id="myChart"></canvas>
        <canvas id="HistoryChart"></canvas>

    </div>
    

</div>
@endsection



@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <script src="{{asset('assets/js/chart.js')}}"></script> --}}

    <script>
        $(document).ready(function() {
            let myChart = null;
            let HistoryChart = null;
            let result;
            let index;
            function showChart(parentCategoryId, childerensOrParent) {
                getDataForChart(parentCategoryId, childerensOrParent)
                console.log(result)             
                showPieChart()
                showLineChart()                               
            }
            function getDataForChart(parentCategoryId, childerensOrParent) {
                var url = "{{route('user.profile.getChartResult')}}";        
                data =  {parentCategoryId : parentCategoryId, childerensOrParent: childerensOrParent} ;
                result = Ajax(url, data)  
            }
            function showPieChart() 
            {
                labels = result.labels
                levels = result.levels
                if(result.ids.length == 1)
                {
                    $("#myChart").hide()
                    return;   
                }
                else
                {
                    $("#myChart").show()
                }
                // console.log(result);

                $(".parentCategoryName").html(result.ParentCategoryName)
                if(result.OriginalParentCategoryId == null)
                {
                    $(".parentBtn").addClass("disabled")
                }
                else
                {
                    $(".parentBtn").removeClass("disabled")
                }

                const ctx = document.getElementById('myChart');
                if(myChart)
                {
                    myChart.destroy()   
                }
            
                myChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: levels,
                             borderWidth: 1
                        }]
                    },
                    options: {
                        onClick: (event, elements) => {
                            if(elements.length > 0 )
                            {
                                index = elements[0].index;
                                showChart(result.ids[index])
                            }
                        }
                    }
                });
            }
            function showLineChart() {
                labels = result.labels
                levels = result.levels

                
                const ctxHistory = document.getElementById('HistoryChart');
                if(HistoryChart)
                {
                    HistoryChart.destroy()   
                }
                    
                level_history = result.level_history
                level_history_times = result.level_history_times

                if(result.ids.length > 1)
                {


                    datasets = []
                    for (i = 0; i < level_history.length; i++) {                   
                        datasets.push({
                            label: labels[i],
                            data: level_history[i],
                            pointRadius : 10, 
                        })
                    }

                    console.log(result)

                    HistoryChart = new Chart(ctxHistory, {
                        type: 'line',
                        data: {
                            labels: level_history_times,
                            datasets: datasets,
                        },
                        options: {

                        }
                    });
                }
                else
                {
                    alert(level_history);
                    HistoryChart = new Chart(ctxHistory, {
                        type: 'line',
                        data: {
                            labels: level_history_times,
                            datasets: [
                                {
                                    label : result.labels,
                                    data : level_history,
                                    pointRadius : 10, 
                                }
                            ],
                        },
                        options: {

                        }
                    });
                }
            }

            $(".parentBtn").click(function () {
                showChart(result.OriginalParentCategoryId)
            })

                      
            showChart(6)

            




        });
    </script>
@endsection