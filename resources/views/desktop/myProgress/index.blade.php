@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/desktop/myProgress/myProgress.css')}}">
<link rel="stylesheet" href="{{asset('assets/persianDatepicker/persian-datepicker.min.css')}}">


@endsection
@section('content')
<div class="QuizResult main-body">
    <div class="chartHeader">
        <button class="parentBtn">بازگشت</button>
        <div class="parentCategoryName"></div>
    </div>    

    <div>
        <canvas id="myChart"></canvas>
        <div class="chooseTimeSpan">
            <div class="from">
                <label for="dateFrom">نمایش از تاریخ :‌</label>
                <input type="text" name="datePickerFrom" id="datePickerFrom">
            </div>
            <div class="to">
                <label for="datePickerTo"> تا تاریخ :‌</label>
                <input type="text" name ="datePickerTo" id="datePickerTo">
            </div>
            <div class="spanTime">
                دقت نمایش: 
                <select class="spanTimeSelect">
                    <option value="hour">ساعتی</option>
                    <option value="day">روزانه</option>
                    <option value="month">ماهیانه</option>
                <select>
            </div>
            <div class="showRefiendData">
                <button class="showRefiendDatabtn">نمایش</button>
            </div>
        </div>
        <canvas id="HistoryChart"></canvas>

    </div>
    

</div>
@endsection



@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{asset('assets/persianDatepicker/persian-date.min.js')}}"></script>
    <script src="{{asset('assets/persianDatepicker/persian-datepicker.min.js')}}"></script>
    {{-- <script src="{{asset('assets/js/chart.js')}}"></script> --}}

    <script>
        $(document).ready(function() {
            function toPersianDigits(str) {
                if (!str) return ""
                return str.replace(/\d/g, function (d) {
                    return "۰۱۲۳۴۵۶۷۸۹"[parseInt(d)]
                })
            }
  
            var today  = new persianDate().toLocale('en').format("YYYY/MM/DD");
            var weekAgo  = new persianDate().subtract('days', 7).toLocale('en').format("YYYY/MM/DD");
            var today = toPersianDigits(today);
            var weekAgo = toPersianDigits(weekAgo);
            $("#datePickerFrom").val(weekAgo)
            $("#datePickerTo").val(today)


            $("#datePickerFrom").persianDatepicker({
                format : "YYYY/MM/DD",
                autoClose : true,
                initialValue : false,          
            })
            $("#datePickerTo").persianDatepicker({
                format : "YYYY/MM/DD",
                autoClose : true,
                initialValue : false,
            })
            
     
            $(".showRefiendDatabtn").click(function() {
                dateFrom = $("#datePickerFrom").val()
                dateTo = $("#datePickerTo").val()
                if(dateFrom>dateTo)
                {
                    $(".failed-message").html("لطفا تاریخ را به نحوی انتخاب کنید که شروع قبل از پایان باشد")   
                    $('.failed-message').show().delay(2000).fadeOut('slow');
                    return;   
                }
                showChart(result.parentCategoryId)
            })


            let myChart = null;
            let HistoryChart = null;
            let result;
            let index;
            let ctx = document.getElementById('myChart');
            let ctxHistory = document.getElementById('HistoryChart');
            function showChart(parentCategoryId) {
                getDataForChart(parentCategoryId)
                console.log(result)
                showPieChart()
                showLineChart()                               
            }
            function getDataForChart(parentCategoryId) {
                var url = "{{route('desktop.getChartResult')}}";        
                data =  {parentCategoryId : parentCategoryId,
                          userId: {{$userId}},
                          datePickerFrom: $("#datePickerFrom").val(),
                          datePickerTo: $("#datePickerTo").val(),
                          spanTimeSelect : $(".spanTimeSelect").val()
                        } ;
                result = Ajax(url, data)  
                console.log(result);
                if(result.OriginalParentCategoryId == null)
                {
                    $(".parentBtn").addClass("disabled")
                }
                else
                {
                    $(".parentBtn").removeClass("disabled")
                }
            }
            function showPieChart() 
            {
                labels = result.labels
                levels = result.levels
                $(".parentCategoryName").html(result.ParentCategoryName)

                if(myChart)
                {
                    myChart.destroy()   
                }
                if(result.ChildrenCount == 0)
                {
                    showSinglePieChart()
                }
                else
                {
                    showMultiplePieChart()
                }            
            }
            function showSinglePieChart() {
                myChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: [result.labels],
                        datasets: [{
                            data: [result.levels],
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

            function showMultiplePieChart() {
                myChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: result.labels,
                        datasets: [{
                            data: result.levels,
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
                if(HistoryChart)
                {
                    HistoryChart.destroy()   
                }                    
                if(result.ChildrenCount == 0)
                {
                    showSingleLineChart()
                }
                else
                {
                    showMultipleLineChart()
                }
            }

            function showSingleLineChart()
            {
                console.log(result.level_history_times)
                HistoryChart = new Chart(ctxHistory, {
                    type: 'line',
                    data: {
                        labels: result.level_history_times,
                        datasets: [
                            {
                                label : [result.labels],
                                data : result.level_history,
                                pointRadius : 10, 
                            }
                        ],
                    },
                    options: {

                    }
                });
            }

            function showMultipleLineChart() {
                datasets = []
                for (i = 0; i < result.level_history.length; i++) {                   
                    datasets.push({
                        label: result.labels[i],
                        data: result.level_history[i],
                        pointRadius : 10, 
                    })
                }
                HistoryChart = new Chart(ctxHistory, {
                    type: 'line',
                    data: {
                        labels: result.level_history_times,
                        datasets: datasets,
                    },
                    options: {

                    }
                });
            }

            $(".parentBtn").click(function () {
                showChart(result.OriginalParentCategoryId)
            })

                      
            showChart()

            




        });
    </script>
@endsection