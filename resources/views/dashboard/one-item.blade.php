@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-sm-3">
            <div class="colored-card act">
                <h3>Депозит</h3>
                <div class="card-body">
                    <strong>
                        {{number_format($p->deposit,2)}} ₽
                    </strong>
                    <i class="icon icon-profit"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="colored-card depo">
                <h3>Текущая оценка портфеля</h3>
                <div class="card-body">
                    <strong>
                        {{number_format($p->balance['rub'], 2)}} ₽
                    </strong>
                    <i class="icon icon-portfolio-white"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="colored-card prof">
                <h3>Доходность портфеля</h3>
                <div class="card-body">
                    <strong>
                        {{number_format($p->profit, 2)}} %
                    </strong>
                    <i class="icon icon-growth"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="colored-card grow">
                <h3>Срок жизни портфеля, дн</h3>
                <div class="card-body">
                    <strong>
                        {{$p->getLifeTime()}}
                    </strong>
                    <i class="icon icon-clock-small"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-sm-6">

            <div class="card">
                <h5 class="card-header">Состав портфеля</h5>
                <div class="card-body">

                    <table class="table table-bordered darken-table">
                        <tr class="dark">
                            <td>Наименование</td>
                            <td>Баланс</td>
                            <td>Доля актива</td>
                        </tr>

                        @foreach($p->assets as $item)
                            @if($item->amount>env('min_amount', 0.000001))
                                <tr>
                                    <td>
                                        {{$item->asset->ticker}}
                                        ({{$item->asset->title}})
                                    </td>
                                    <td class="text-right">
                                        <span v-if="1==1">
                                            {{number_format($item->amount, 1)}}
                                        </span>
                                        <span v-else>
                                            {{number_format($item->amount, 5)}}
                                        </span>
                                    </td>
                                    <td class="text-right">{{number_format($item->getShare(),2)}} %</td>
                                </tr>
                            @endif
                        @endforeach

                    </table>

                </div>
                <div class="card-footer">
                    <a href="/portfolio/{{$p->id}}">Перейти в портфель</a>
                </div>
            </div>

        </div>

        <div class="col-sm-6">

            <div class="card">
                <h5 class="card-header">График распределения по долям</h5>
                <div class="card-body">
 
                    
                    
                    <!--canvas id="piechart" width="400px" height="400px"></canvas-->

                </div>
            </div>

        </div>
    </div>
    <div id="chartdiv" width="600px" height="600px"></div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script src="/js/aWapBE.js"></script>
    
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/pie.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>


    <script>
        
        
        setTimeout(function(){
        
            var chart = AmCharts.makeChart( "chartdiv", {
                                          "type": "pie",
                                          "theme": "light",
                                          "dataProvider": [
                                              {"asset":"Bitcoin","value":"37.08"},
                                              {"asset":"Ripple","value":"0.31"},
                                              {"asset":"DigiByte","value":"0.31"},
                                              {"asset":"Bela","value":"0.03"},
                                              {"asset":"TrustPlus","value":"0.27"},
                                              {"asset":"BitShares","value":"0.55"},
                                              {"asset":"GameCredits","value":"0.17"},
                                              {"asset":"Tether","value":"56.79"},
                                              {"asset":"NEM","value":"0.59"},
                                              {"asset":"Synereo","value":"0.19"},
                                              {"asset":"Steem","value":"0.55"},
                                              {"asset":"Waves","value":"0.27"},
                                              {"asset":"Ethereum Classic","value":"0.28"},
                                              {"asset":"Stratis","value":"0.34"},
                                              {"asset":"Iconomi","value":"0.24"},
                                              {"asset":"Golem","value":"0.11"},
                                              {"asset":"Nexium","value":"0.12"},
                                              {"asset":"Wings","value":"0.14"},
                                              {"asset":"Edgeless","value":"0.65"},
                                              {"asset":"Matchpool","value":"0.17"},
                                              {"asset":"Aragon","value":"0.44"}],
                                          "valueField": "value",
                                          "titleField": "asset",
                                          "outlineAlpha": 0.1,
                                          "depth3D": 15,
                                          "balloonText": "[[title]]<br><span style='font-size:10px'><b>[[value]]%</b></span>",
                                          "angle": 30,
                                          "export": {
                                            "enabled": false
                                          }
                                        } );
                console.log(chart.chartData); },
                   
                   3000);
        
        
        
        
        
        $(function () {
            return;
            new Chart(document.getElementById("piechart"), {
                options: {
                    responsive: true,
                    legend: {
                        position: 'left',
                    }
                },
                "type": "doughnut",
                "data": {
                    "labels": {!! json_encode($labels) !!},
                    "datasets": [{
                        "label": "",
                        "data": {!! json_encode($chartData) !!},
                        "backgroundColor": palette('qualitative', {{count($chartData)}}).map(function (hex) {
                            return '#' + hex;
                        })
                    }],
                }
            });

        });
    </script>
@endsection