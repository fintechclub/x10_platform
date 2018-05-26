@extends('layouts.app')

@section('content')

    <div class="row">
        @foreach($portfolios as $p)
            <div class="col-sm-4">

                <div class="card cursor-pointer" onclick="location.href='/portfolio/{{$p->id}}'">
                    <h5 class="card-header">Портфель #{{$p->id}}</h5>
                    <div class="card-body">
                        <strong>$ {{$p->getBalance('usd')}}</strong>
                        <span>{{$p->getStat()}}</span>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

@endsection