@extends('layouts.app')

@section('content')

    <h1>Пользователи и портфели</h1>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered">
                <tr>
                    <th>Имя</th>
                    <th>Почта</th>
                    <th>Телефон</th>
                    <th>Портфели</th>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td>{{$user->name}} {{$user->sname}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->phone}}</td>
                        <td>
                            <ul class="list-unstyled">
                            @foreach($user->portfolios as $p)
                                <li>
                                    <a href="/users/{{$user->id}}/portfolio/{{$p->id}}">
                                        Портфель #{{$p->id}}
                                    </a>
                                </li>
                            @endforeach
                                <li class="mt-4">
                                    <form action="/api/portfolio/create" method="post">
                                        <input type="hidden" name="customer_id" value="{{$user->id}}"/>
                                        {{csrf_field()}}
                                        <button type="submit" class="btn btn-primary btn-sm">Добавить новый</button>
                                    </form>
                                </li>
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection