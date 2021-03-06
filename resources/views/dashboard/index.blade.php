@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-sm-3">
            <div class="colored-card act">
                <h3>Активные портфели</h3>
                <div class="card-body">
                    <strong>
                        {{$total}}
                    </strong>
                    <i class="icon icon-portfolio-white"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="colored-card depo">
                <h3>Суммарный депозит</h3>
                <div class="card-body">
                    <strong>
                        {{number_format($userData['deposit'], 2)}} ₽
                    </strong>
                    <i class="icon icon-deposit"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="colored-card prof">
                <h3>Суммарная доходность</h3>
                <div class="card-body">
                    <strong>
                        {{number_format($userData['profit'],2)}} %
                    </strong>
                    <i class="icon icon-profit"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="colored-card grow">
                <h3>Прирост по портфелям</h3>
                <div class="card-body">
                    <strong>
                        {{number_format($userData['growth'],2)}} ₽
                    </strong>
                    <i class="icon icon-growth"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($portfolios as $p)
            <div class="col-sm-6">
                <div class="card p-item cursor-pointer" onclick="location.href='/dashboard/{{$p->id}}'">
                    <h3 class="card-header">{{$p->getTitle()}}</h3>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>
                                    <small>Депозит</small>
                                    <strong>{{number_format($p->deposit,2)}} ₽</strong>
                                </td>
                                <td>
                                    <small>Текущая оценка портфеля</small>
                                    <strong>{{number_format($p->balance['rub'], 2)}} ₽</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <small>Доходность портфеля, %</small>
                                    <strong class="{{$p->profit>0 ? 'green': 'red'}}">{{number_format($p->profit,2)}}</strong>
                                </td>
                                <td>
                                    <small>Баланс, BTC</small>
                                    <strong>{{number_format($p->balance['btc'], 5)}}</strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection