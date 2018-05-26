@extends('layouts.app')

@section('content')


    <div class="row">
        <div class="col-sm-12">
            <h1>{{$user->name}}</h1>
        </div>

        <div class="col-sm-12 mb-4">
            <form action="/api/portfolio/create" method="post">
                {{csrf_field()}}
                <input type="hidden" name="user_id" value="{{$user->id}}"/>
                <button type="submit" class="btn btn-primary btn-sm">Добавить новый портфель</button>
            </form>
        </div>

        <div class="col-sm-12">
            <div class="row">
                @foreach($user->portfolios as $p)
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">#{{$p->id}}</h5>
                                <p class="card-text"></p>
                                <a href="/users/{{$user->id}}/portfolio/{{$p->id}}" class="btn btn-light btn-sm">Смотреть</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>

    <div class="row">
        <div class="col-sm-12"></div>
    </div>
@endsection