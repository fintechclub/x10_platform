@extends('layouts.app')

@section('content')

    <div class="row">
        @foreach($portfolios as $p)
            <div class="col-sm-4 mb-4">

                <div class="card cursor-pointer" onclick="location.href='/portfolio/{{$p->id}}'">
                    <h5 class="card-header">Портфель #{{$p->id}}</h5>
                    <div class="card-body portfolio-item">
                        <span class="text-bigger">${{$p->getBalanceUsd()}}</span>
                        <span class="text-bigger">{{$p->getLastChange()}}</span>

                        <i class="icon icon-portfolio"></i>
                    </div>

                </div>

            </div>
        @endforeach
    </div>

@endsection