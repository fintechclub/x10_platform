@extends('layouts.app')

@section('content')
    <div class="row settings" id="settings">
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

                        <form @submit.prevent="savePersonal" a>
                            <div class="form-group">
                                <label>Имя</label>
                                <input type="text" class="form-control" v-model="personal.name" placeholder="Имя"
                                       required maxlength="200"/>
                            </div>

                            <div class="form-group">
                                <label>Фамилия</label>
                                <input type="text"
                                       class="form-control" v-model="personal.sname" placeholder="Фамилия"
                                       required  maxlength="200"/>
                            </div>

                            <div class="form-group">
                                <label>Дата рождения</label>
                                <input type="date" max="{{date('Y-m-d')}}" class="form-control" v-model="personal.date_birth"
                                       placeholder="Дата рождения"/>
                            </div>

                            <div class="form-group">
                                <label>Телефон</label>
                                <input type="text"  maxlength="12" class="form-control" v-model="personal.phone" placeholder="Телефон"/>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email"  maxlength="200"
                                       class="form-control" v-model="personal.email" placeholder="Email"
                                       required/>
                            </div>

                            <div class="text-right">
                                <button class="btn btn-sm btn-cancel" @click.prevent="cancelEditPersonal()"
                                        type="button">Отмена
                                </button>
                                <button class="btn btn-sm btn-bordered"
                                        type="submit"
                                        :disabled="!checkPersonalSettingsForm()">Сохранить
                                </button>
                            </div>
                        </form>

                    </div>

                    <div class="text-right">
                        <button class="btn btn-bordered btn-sm" @click="enableEditPersonal()"
                                v-if="!editPersonal">Изменить
                        </button>
                    </div>
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
                                   @keyup.enter="checkCurrentPassword()"
                                   placeholder="Пароль"/>
                        </div>
                        <div class="alert alert-warning" v-if="step1failed">
                            Неверный пароль
                        </div>

                        <div class="text-right">
                            <button class="btn btn-sm btn-cancel" @click="editSecurity=false">Отмена</button>
                            <button class="btn btn-sm btn-bordered"
                                    :class="{busy: security.busyStep1}"
                                    @click="checkCurrentPassword()">Далее
                            </button>
                        </div>
                    </div>

                    <div class="step-1" v-if="editSecurity && step2">
                        <div class="form-group">
                            <label>Новый пароль</label>
                            <input type="password"
                                   @keyup.enter="savePassword()"
                                   class="form-control" v-model="security.new_password"
                                   placeholder="Пароль"/>
                        </div>
                        <div class="form-group">
                            <label>Новый пароль</label>
                            <input type="password"
                                   @keyup.enter="savePassword()"
                                   class="form-control" v-model="security.confirm"
                                   placeholder="Подтверждение"/>
                        </div>

                        <ul class="list-unstyled">
                            <li :class="security.rules[3] ? 'success' : 'warning'">1 цифра</li>
                            <li :class="security.rules[1] ? 'success' : 'warning'">1 маленькая буква</li>
                            <li :class="security.rules[2] ? 'success' : 'warning'">1 большая буква</li>
                            <li :class="security.rules[4] ? 'success' : 'warning'">Минимум 8 символов</li>
                            <li :class="security.rules[0] ? 'success' : 'warning'">Пароли должны совпадать</li>
                        </ul>

                        <div class="text-right">
                            <button class="btn btn-sm btn-cancel" @click="editSecurity=false">Отмена</button>
                            <button class="btn btn-sm btn-bordered"
                                    :disabled="!checkPasswordForm()"
                                    :class="{busy: security.busyStep2}"
                                    @click="savePassword()">Сохранить
                            </button>
                        </div>
                    </div>

                    <div class="step-2"></div>

                    <div class="text-right">
                        <button class="btn btn-sm btn-bordered" @click="editSecurity=true" v-if="!editSecurity">Изменить
                            пароль
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <h5 class="card-header">Смена темы</h5>
                <div class="card-body">
                    <small class="text-muted">Функция в разработке</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <h5 class="card-header">Аватар</h5>
                <div class="card-body">
                    <small class="text-muted">Функция в разработке</small>
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
                personalBackup: {},
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
            mounted: function () {

            },
            methods: {
                enableEditPersonal(){
                    Object.assign(this.personalBackup, this.personal);
                    this.editPersonal = true;
                },
                cancelEditPersonal(){
                    Object.assign(this.personal, this.personalBackup);
                    this.editPersonal = false;
                },
                savePersonal(){

                    // validate form
                    if (!this.checkPersonalSettingsForm()) {
                        return false;
                    }

                    axios.post('/user/settings/personal/save', this.personal).then(response => {
                        this.editPersonal = false;
                        // add notification
                        this.notify('Персональные данные обновлены');
                    });

                },
                // personal settings form validation
                checkPersonalSettingsForm(){

                    let pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    let email = String(this.personal.email).toLowerCase()

                    if (!pattern.test(email) || email === '' || this.personal.name === '' || this.personal.sname === '') {
                        return false;
                    }

                    return true;

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

                    // check password form firstly
                    if(!this.checkPasswordForm()){
                        return false;
                    }

                    this.security.busyStep2 = true;
                    axios.post('/user/settings/security/save-password', {password: this.security.new_password}).then(response => {

                        let status = response.data.status;

                        if (status === 'success') {
                            this.editSecurity = false;
                            this.security.new_password = '';
                            this.security.confirm = '';
                            this.security.password = '';
                            this.notify('Пароль успешно изменен');
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

                },
                notify(text){
                    $.notify(text, {
                        className: 'success',
                        position: "right bottom"
                    });
                }

            }
        });
    </script>
@endsection