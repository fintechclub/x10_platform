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
                <h3>Срок действия портфеля, дн</h3>
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
                                    <td class="text-right">{{number_format($item->amount, 5)}}</td>
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
                <h5 class="card-header">Распределение по долям</h5>
                <div class="card-body">
                    <canvas id="piechart" width="400px" height="500px"></canvas>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script src="/js/aWapBE.js"></script>

    <script>
        $(function () {
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