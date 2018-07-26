@extends('layouts.app')

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/">Пользователи и портфели</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$user->name}} {{$user->sname}}</li>
        </ol>
    </nav>


    <div class="row">
        <div class="col-sm-12">
            <h1>{{$user->name}}</h1>

            <form action="/api/portfolio/create" method="post" class="mb-4">
                {{csrf_field()}}
                <input type="hidden" name="customer_id" value="{{$user->id}}"/>
                <button type="submit" class="btn btn-primary btn-sm">Добавить новый портфель</button>
            </form>


        </div>

        <div class="col-sm-12">

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>id</th>
                            <th>Название</th>
                            <th>Депозит</th>
                            <th colspan="2"></th>
                        </tr>
                        @foreach($user->portfolios as $p)
                            <tr>
                                <td>
                                    <a href="/users/{{$user->id}}/portfolio/{{$p->id}}"
                                       class="btn btn-light btn-sm">#{{$p->id}} <i class="fas fa-eye"></i></a>
                                </td>
                                <td>
                                    <form method="post" action="/admin/users/update-portfolio/{{$p->id}}">
                                        {{csrf_field()}}
                                        <div class="form-group">
                                            <input type="text" name="title" class="form-control" value="{{$p->title}}"/>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary">Сохранить</button>
                                    </form>
                                </td>
                                <td>{{number_format($p->deposit,2)}} ₽</td>
                                <td>
                                    <a href="/admin/users/clear-portfolio/{{$p->id}}" class="btn btn-warning btn-sm"
                                       onclick="confirm('Подверждаете удаление всех транзакций в портфеле и сбросить всю статистику?')">
                                        Сбросить в нач. состояние
                                    </a>

                                </td>
                                <td>
                                    <form action="/admin/users/import-portfolio" method="post"
                                          enctype="multipart/form-data">

                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="file" class="custom-file-input"
                                                       id="inputGroupFile04">
                                                <label class="custom-file-label" for="inputGroupFile04">Выбрать
                                                    CSV транзакции</label>
                                            </div>
                                        </div>

                                        <div class="input-group mt-2">
                                            <div class="custom-file">
                                                <input type="file" name="file_dynamic" class="custom-file-input"
                                                       id="inputGroupFile04">
                                                <label class="custom-file-label" for="inputGroupFile04">Выбрать
                                                    CSV динамика</label>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary mt-2" type="submit">Импортировать
                                        </button>

                                        <input type="hidden" name="user_id" value="{{$user->id}}"/>
                                        <input type="hidden" name="portfolio_id" value="{{$p->id}}"/>

                                        {{csrf_field()}}
                                    </form>


                                </td>
                            </tr>

                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection