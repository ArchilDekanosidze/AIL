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
            function showChart(parentCategoryId, childerensOrParent) {

                var url = "{{route('user.profile.getChartResult')}}";        
                data =  {parentCategoryId : parentCategoryId, childerensOrParent: childerensOrParent} ;
                result = Ajax(url, data)

                $(".parentCategoryName").html(result.ParentCategoryName)
                if(result.OriginalParentCategoryId == null)
                {
                    $(".parentBtn").addClass("disabled")
                }
                else
                {
                    $(".parentBtn").removeClass("disabled")
                }

                labels = result.labels
                levels = result.levels

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
                                showChart(result.ids[index], "child")
                            }
                        }
                    }
                });

                const ctxHistory = document.getElementById('HistoryChart');
                if(HistoryChart)
                {
                    HistoryChart.destroy()   
                }
            
                level_history = result.level_history
                level_history_times = result.level_history_times

                let allLabels = new Set();

                level_history_times.forEach(elements => {
                    elements.forEach(element => {
                        allLabels.add(element)
                    });
                });
                allLabels = Array.from(allLabels).sort();
                console.log(allLabels)
                datasets = []
                for (i = 0; i < level_history.length; i++) {
                    newLevelHistory = [];
                    for(j = 0; j<level_history_times[i].length; j++)
                    {
                        newLevelHistory = level_history
                        console.log(level_history)
                        // console.log(level_history_times[i][j])
                        // console.log(allLabels)
                        // console.log(jQuery.inArray(level_history_times[j][j], allLabels))
                    }
                    datasets.push({
                        label: labels[i],
                        data: data,
                        pointRadius : 10, 
                    })
                }


                HistoryChart = new Chart(ctxHistory, {
                    type: 'line',
                    data: {
                        labels: allLabels,
                        datasets: datasets,
                    },
                    options: {

                    }
                });







            }

            $(".parentBtn").click(function () {
                showChart(result.OriginalParentCategoryId, "child")
            })

                      
            showChart(6, "child")

            




        });
    </script>
@endsection