@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-sm-4">
            @foreach($users as $user)
                <a href="/admin/users/{{$user->id}}">{{$user->name}}</a>
            @endforeach
        </div>
        <div class="col-sm-6">
            <h3>Добавить новый портфель</h3>
            <form action="/api/portfolio/create" method="post">
                {{csrf_field()}}

                <div class="form-group">
                    <label>Клиент</label>
                    <select name="customer_id">
                        @foreach($users as $user)
                            <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Добавить</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12"></div>
    </div>
@endsection