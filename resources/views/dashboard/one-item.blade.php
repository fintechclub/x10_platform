@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-sm-3">
            <div class="colored-card act">
                <h3>Депозит</h3>
                <div class="card-body">
                    <strong>
                        {{number_format($p->deposit,2)}} Р.
                    </strong>
                    <i class="icon-flat icon-wallet"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="colored-card depo">
                <h3>Текущая оценка портфеля</h3>
                <div class="card-body">
                    <strong>
                        {{$p->getBalance('rub',true)}} Р.
                    </strong>
                    <i class="icon-flat icon-case"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="colored-card prof">
                <h3>Доходность портфеля</h3>
                <div class="card-body">
                    <strong>
                        {{$p->getTotalProfit()}} %
                    </strong>
                    <i class="icon-flat icon-increase"></i>
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
                    <i class="icon-flat icon-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-sm-6">

            <div class="card">
                <h5 class="card-header">Состав портфеля</h5>
                <div class="card-body">

                    <table class="table table-bordered darken-table table-striped">
                        <tr class="dark">
                            <td>Наименование</td>
                            <td>Баланс</td>
                            <td>Изменение цены</td>
                        </tr>

                        @foreach($p->assets as $item)
                            @if($item->amount>env('min_amount', 0.000001))
                                <tr>
                                    <td>{{$item->asset->title}}</td>
                                    <td>{{number_format($item->amount,5)}}</td>
                                    <td></td>
                                </tr>
                            @endif
                        @endforeach

                    </table>

                </div>
            </div>

        </div>

        <div class="col-sm-6">

            <div class="card">
                <h5 class="card-header">Pie Chart</h5>
                <div class="card-body">

                    <canvas id="piechart" width="400" height="400"></canvas>

                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script src="https://codepen.io/anon/pen/aWapBE.js"></script>
    <script>
        $(function () {
            new Chart(document.getElementById("piechart"), {
                "type": "doughnut",
                "data": {
                    "labels": {!! json_encode($labels) !!},
                    "datasets": [{
                        "label": "",
                        "data": {!! json_encode($chartData) !!},
                        "backgroundColor": palette('tol', {{count($chartData)}}).map(function (hex) {
                            return '#' + hex;
                        })
                    }],
                }
            });

        });
    </script>
@endsection