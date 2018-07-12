@extends('layouts.app')

@section('content')

    @if(count($portfolios)==0)
        <div class="centered-block">

            <i class="icon-presentation"></i>
            <p class="lead">
                Пока у Вас нет инвестиционных портфелей. <br/>
                Cвяжитесь с нами  <a href="mailto: info@x10.fund">info@x10.fund</a> <br/> и мы поможем Вам составить оптимальный инвестиционный портфель.
            </p>
        </div>

    @endif

    <div class="row">
        @foreach($portfolios as $p)
            <div class="col-sm-4 mb-4">

                <div class="card cursor-pointer" onclick="location.href='/portfolio/{{$p->id}}'">
                    <h5 class="card-header">Портфель #{{$p->id}}</h5>
                    <div class="card-body portfolio-item">
                        <span class="text-bigger">{{$p->getBalance('rub',true)}} ₽</span>
                        <span class="text-bigger {{$p->getTotalProfit()>0 ? 'green': 'red'}}">{{$p->getTotalProfit()}} %</span>

                        <i class="icon icon-portfolio"></i>
                    </div>

                </div>

            </div>
        @endforeach
    </div>

@endsection