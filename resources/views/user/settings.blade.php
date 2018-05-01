@extends('layouts.app')

@section('content')
    <h1>Настройки</h1>

    <div class="row" id="settings">
        <div class="col-sm-3">
            <div class="card">
                <h5 class="card-header">Персональные данные</h5>
                <div class="card-body">
                    <div class="preview" v-if="!editPersonal">
                        <dl>
                            <dt>Имя</dt>
                            <dd>@{{ personal.name }}</dd>
                            <dt>Фамилия</dt>
                            <dd>@{{ personal.sname }}</dd>
                            <dt>Дата рождения</dt>
                            <dd>@{{ personal.date_birth }}</dd>
                            <dt>Телефон</dt>
                            <dd>@{{ personal.phone }}</dd>
                            <dt>Email</dt>
                            <dd>@{{ personal.email }}</dd>
                        </dl>
                    </div>
                    <div class="edit" v-if="editPersonal">

                        <div class="form-group">
                            <input type="text" class="form-control" v-model="personal.name" placeholder="Имя"/>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" v-model="personal.sname" placeholder="Фамилия"/>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" v-model="personal.date_birth" placeholder="Дата рождения"/>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" v-model="personal.phone" placeholder="Телефон"/>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" v-model="personal.email" placeholder="Email"/>
                        </div>

                        <button class="btn btn-sm btn-default" @click="editPersonal=false">Отмена</button>
                        <button class="btn btn-sm btn-primary" @click="savePersonal()">Сохранить</button>

                    </div>

                    <a href="#" class="btn btn-default btn-sm" @click="editPersonal=true" v-if="!editPersonal">Изменить</a>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <h5 class="card-header">Безопасность</h5>
                <div class="card-body">

                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <h5 class="card-header">Смена темы</h5>
                <div class="card-body">

                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <h5 class="card-header">Аватар</h5>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var settings = new Vue({
            el: '#settings',
            data: {
                editPersonal: false,
                personal: {
                    name: '{{Auth::user()->name}}',
                    sname: '{{Auth::user()->sname}}',
                    date_birth: '{{Auth::user()->date_birth}}',
                    phone: '{{Auth::user()->phone}}',
                    email: '{{Auth::user()->email}}'
                }
            },
            methods: {
                savePersonal(){
                    axios.post('/user/settings/personal/save', this.personal).then(response => {
                        this.editPersonal = false;
                    });
                }
            }
        });
    </script>
@endsection