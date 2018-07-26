@extends('layouts.app')

@section('content')

    <h1>Пользователи и портфели</h1>

    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">Пользователи</div>
                <div class="card-body">

                    <table class="table table-bordered">
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <a href="/admin/users/{{$user->id}}">
                                        <strong>{{$user->name}} {{$user->sname}}</strong>
                                    </a>
                                    <br/>
                                    <small class="help-block">
                                        {{$user->email}} <br/>
                                        {{$user->phone}}
                                    </small>
                                    <br/>
                                    @if($user->tmp_pwd)
                                        <small>
                                            Временный пароль:
                                            <span class="badge badge-dark">
                                                {{$user->tmp_pwd}}
                                            </span>
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        @foreach($user->portfolios as $p)
                                            <li>
                                                <a href="/users/{{$user->id}}/portfolio/{{$p->id}}">
                                                    {{$p->getTitle()}}
                                                </a>
                                            </li>
                                        @endforeach
                                        <li class="mt-4">
                                            <form action="/api/portfolio/create" method="post">
                                                <input type="hidden" name="customer_id" value="{{$user->id}}"/>
                                                {{csrf_field()}}
                                                <button type="submit" class="btn btn-primary btn-sm">Добавить новый
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">Новый пользователь</div>
                <div class="card-body">
                    <form method="post" action="/admin/users/create">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="formGroupExampleInput">Имя</label>
                            <input type="text" class="form-control" id="formGroupExampleInput" name="name"
                                   placeholder="Имя" required>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Фамилия</label>
                            <input type="text" class="form-control" id="formGroupExampleInput" name="sname"
                                   placeholder="Фамилия" required>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput">Телефон</label>
                            <input type="text" class="form-control" id="formGroupExampleInput" name="phone"
                                   placeholder="Телефон" required>
                        </div>
                        <div class="form-group">
                            <label for="formGroupExampleInput2">Email</label>
                            <input type="text" class="form-control" id="formGroupExampleInput2" name="email"
                                   placeholder="Email" required>
                        </div>

                        <button type="submit" class="btn btn-success">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection