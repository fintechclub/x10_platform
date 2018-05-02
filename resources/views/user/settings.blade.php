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
                            <input type="text" class="form-control" v-model="personal.date_birth"
                                   placeholder="Дата рождения"/>
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

                    <a href="#" class="btn btn-default btn-sm" @click="editPersonal=true"
                       v-if="!editPersonal">Изменить</a>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <h5 class="card-header">Безопасность</h5>
                <div class="card-body">

                    <div class="step-1" v-if="editSecurity && !step2">
                        <div class="form-group">
                            <label>Текущий пароль</label>
                            <input type="password" class="form-control" v-model="security.password"
                                   placeholder="Пароль"/>
                        </div>
                        <div class="alert alert-warning" v-if="step1failed">
                            Неверный пароль
                        </div>
                        <button class="btn btn-sm btn-default" @click="editSecurity=false">Отмена</button>
                        <button class="btn btn-sm btn-primary"
                                :class="{busy: security.busyStep1}"
                                @click="checkCurrentPassword()">Далее
                        </button>
                    </div>

                    <div class="step-1" v-if="editSecurity && step2">
                        <div class="form-group">
                            <label>Новый пароль</label>
                            <input type="password" class="form-control" v-model="security.new_password"
                                   placeholder="Пароль"/>
                        </div>
                        <div class="form-group">
                            <label>Новый пароль</label>
                            <input type="password" class="form-control" v-model="security.confirm"
                                   placeholder="Подтверждение"/>
                        </div>

                        <ul class="list-unstyled">
                            <li :class="security.rules[3] ? 'success' : 'warning'">1 цифра</li>
                            <li :class="security.rules[1] ? 'success' : 'warning'">1 маленькая буква</li>
                            <li :class="security.rules[2] ? 'success' : 'warning'">1 большая буква</li>
                            <li :class="security.rules[4] ? 'success' : 'warning'">Минимум 8 символов</li>
                            <li :class="security.rules[0] ? 'success' : 'warning'">Пароли должны совпадать</li>
                        </ul>

                        <button class="btn btn-sm btn-default" @click="editSecurity=false">Отмена</button>
                        <button class="btn btn-sm btn-primary"
                                :disabled="!checkPasswordForm()"
                                :class="{busy: security.busyStep2}"
                                @click="savePassword()">Сохранить
                        </button>
                    </div>

                    <div class="step-2"></div>

                    <button class="btn btn-sm btn-default" @click="editSecurity=true" v-if="!editSecurity">Изменить
                        пароль
                    </button>
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
                editSecurity: false,
                step1failed: false,
                step1: false,
                step2: false,
                personal: {
                    name: '{{Auth::user()->name}}',
                    sname: '{{Auth::user()->sname}}',
                    date_birth: '{{Auth::user()->date_birth}}',
                    phone: '{{Auth::user()->phone}}',
                    email: '{{Auth::user()->email}}'
                },
                security: {
                    password: '',
                    new_password: '',
                    confirm: '',
                    busyStep1: false,
                    busyStep2: false,
                    rules: [
                        false, false, false, false, false
                    ]
                }
            },
            methods: {
                savePersonal(){
                    axios.post('/user/settings/personal/save', this.personal).then(response => {
                        this.editPersonal = false;
                    });
                },
                checkCurrentPassword(){

                    this.security.busyStep1 = true;
                    this.step1failed = false;

                    axios.post('/user/settings/security/check-password', {
                        password: this.security.password
                    }).then(response => {
                        let status = response.data.status;
                        if (status === 'success') {
                            this.step1 = false;
                            this.step2 = true;
                        } else {
                            this.step1failed = true;
                        }

                        this.security.busyStep1 = false;

                    });
                },
                savePassword(){

                    this.security.busyStep2 = true;
                    axios.post('/user/settings/security/save-password', {password: this.security.new_password}).then(response => {

                        let status = response.data.status;

                        if (status === 'success') {
                            this.editSecurity = false;
                            this.security.new_password = '';
                            this.security.confirm = '';
                            this.security.password = '';
                        }

                        this.security.busyStep2 = false;
                        this.resetSecurityForm();

                    });

                },
                checkPasswordForm(){

                    this.security.rules = [
                        false, false, false, false, false
                    ];

                    let regex = [
                        '',
                        RegExp('(?=.*[a-z])'),
                        RegExp('(?=.*[A-Z])'),
                        RegExp('(?=.*[0-9])'),
                        RegExp('[a-zA-Z0-9]{8,}')
                    ];

                    let password = this.security.new_password;
                    let confirm = this.security.confirm;

                    if (password === confirm && password.length > 0) {
                        this.security.rules[0] = true;
                    }

                    // check the number
                    if (regex[1].test(password)) {
                        this.security.rules[1] = true;
                    }

                    if (regex[2].test(password)) {
                        this.security.rules[2] = true;
                    }

                    if (regex[3].test(password)) {
                        this.security.rules[3] = true;
                    }
                    if (regex[4].test(password)) {
                        this.security.rules[4] = true;
                    }

                    return this.security.rules.every(elem => elem === true);
                },
                resetSecurityForm(){

                    this.editSecurity = false;
                    this.step1failed = false;
                    this.step1 = false;
                    this.step2 = false;
                    this.security.password = '';

                }

            }
        });
    </script>
@endsection