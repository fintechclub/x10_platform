<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&amp;subset=cyrillic"
          rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/auth.css?v={{env('APP_VER',time())}}"/>
    <title>x10.fund</title>
</head>
<body>


<div class="auth-form" id="auth">
    <div class="logo"></div>
    <div class="form-signin">

        <div id="auth" v-if="screen=='login'" :class="{error: loginForm.error.length>0}">
            <h2 class="mb-4 mt-2">Вход в личный кабинет</h2>

            <p class="text-danger error-msg" v-if="loginForm.error.length>0">@{{ loginForm.error }}</p>
            <div class="email mb-2">
                <input type="email" v-model="loginForm.email" id="inputEmail" class="form-control"
                       placeholder="Электронная почта"
                       required=""
                       @keyup.enter="doLogin()"
                       autofocus="">
            </div>
            <div class="password">
                <input type="password"
                       v-model="loginForm.password"
                       @keyup.enter="doLogin()"
                       id="inputPassword" class="form-control"
                       placeholder="Пароль" required="">
            </div>
            <a href="" class="btn-restore" @click.prevent="screen='restore'">Забыли пароль?</a> <br/>
            <button class="btn btn-primary mt-2 btn-block ml-auto mr-auto" @click="doLogin()"
                    :disabled="!checkLoginForm() || loginForm.busy" :class="{busy: loginForm.busy}">Войти
            </button>
        </div>

        {{-- Password restore request --}}
        <div id="auth" v-if="screen=='restore'">
            <h2 class="mt-2">Восстановление пароля</h2>
            <p class="caption mb-5">На вашу почту будет отправлено письмо с инструкцией</p>

            <p class="text-danger error-msg" v-if="restoreForm.error.length>0">@{{ restoreForm.error }}</p>

            <div class="email">
                <input type="email" id="inputEmail" class="form-control" placeholder="Электронная почта"
                       required=""
                       @keyup.enter="doRestore()"
                       v-model="restoreForm.email"
                       autofocus="">
            </div>

            <a href="" class="btn-restore" @click.prevent="screen='login'">Уже вспомнили?</a> <br/>
            <button class="btn btn-primary mt-2 btn-block ml-auto mr-auto"
                    @click="doRestore()" :class="{busy: restoreForm.busy}"
                    :disabled="restoreForm.email.length==0 || restoreForm.busy || !checkRestoreForm()"
            >Сбросить пароль
            </button>
        </div>

        <div id="auth" v-if="screen=='restore-success'">
            <h2 class="mb-4 mt-2 text-green">Восстановление пароля</h2>

            <p class="lead mt-5 mb-5">
                Инструкции высланы на почту.
            </p>

        </div>

        {{-- Password activation --}}
        <div id="auth" v-if="screen=='activate'">
            <h2 class="mb-4 mt-2">Восстановление пароля</h2>

            <div class="row">
                <div class="col-sm-6 recovery-block">
                    <div class="input-layer">
                        <input type="password"
                               v-model="activateForm.password"
                               @keyup.enter="doActivate()"
                               class="form-control"
                               placeholder="Новый пароль" required="">
                    </div>
                    <div class="input-layer mt-3">
                        <input type="password"
                               v-model="activateForm.confirm"
                               @keyup.enter="doActivate()"
                               class="form-control"
                               placeholder="Повторите пароль" required="">
                    </div>
                    <button class="btn btn-primary mt-3 btn-block ml-auto mr-auto"
                            @click="doActivate()" :class="{busy: activateForm.busy}"
                            :disabled="!checkActivateForm() || activateForm.busy"
                    >Сохранить
                    </button>
                </div>
                <div class="col-sm-6 text-left">
                    <ul class="list-unstyled">
                        <li :class="activateForm.rules[3] ? 'success' : 'warning'">1 цифра</li>
                        <li :class="activateForm.rules[1] ? 'success' : 'warning'">1 маленькая буква</li>
                        <li :class="activateForm.rules[2] ? 'success' : 'warning'">1 большая буква</li>
                        <li :class="activateForm.rules[4] ? 'success' : 'warning'">Минимум 8 символов</li>
                        <li :class="activateForm.rules[0] ? 'success' : 'warning'">Пароли должны совпадать</li>
                    </ul>
                </div>
            </div>

        </div>
        <div id="auth" v-if="screen=='activate-success'">
            <h2 class="mb-4 mt-2 text-green">Пароль успешно изменен</h2>

            <p class="lead mt-5 mb-5">
                В течение 3 секунд вы будете перенаправлены в личный кабинет.
            </p>

            <a href="/dashboard">Перейти сразу</a>
        </div>


    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script>

    var auth = new Vue({
        el: '#auth',
        data: {
            screen: '{{$screen}}',
            loginForm: {
                email: '',
                password: '',
                error: '',
                busy: false
            },
            restoreForm: {
                email: '',
                error: '',
                busy: false
            },
            activateForm: {
                password: '',
                id: '{{$id}}',
                token: '{{$token}}',
                confirm: '',
                error: '',
                busy: false,
                rules: [
                    false, false, false, false, false
                ]
            }
        },
        methods: {
            // login user with credentials
            doLogin() {

                if (!this.checkLoginForm()) {
                    return false;
                }

                this.loginForm.error = '';
                this.loginForm.busy = true;
                axios.post('/auth/check-credentials', this.loginForm).then(response => {
                    let status = response.data.status;
                    if (status === 'success') {
                        window.location = '/dashboard';
                    } else {
                        this.loginForm.error = response.data.msg;
                    }
                    this.loginForm.busy = false;
                });
            },
            // restore the password
            doRestore(){

                this.restoreForm.busy = true;
                this.restoreForm.error = '';

                // send email with new password activation link if such user exists
                axios.post('/auth/restore-password', this.restoreForm).then(response => {

                    let status = response.data.status;

                    if (status === 'success') {
                        this.screen = 'restore-success';
                    } else {
                        this.restorF.error = response.data.msg;
                    }

                    this.restoreForm.busy = false;

                });

            },
            // activate new password
            doActivate(){

                if (!this.checkActivateForm()) {
                    return false;
                }

                this.activateForm.busy = true;
                axios.post('/auth/activate-password', this.activateForm).then(response => {

                    let status = response.data.status;
                    this.activateForm.busy = false;

                    if (status === 'success') {

                        this.screen = 'activate-success';

                        setTimeout(function () {
                            window.location = '/dashboard';
                        }, 3000);

                    }
                });

            },
            checkLoginForm(){

                if (this.loginForm.email.length < 6 || this.loginForm.password.length < 8) {
                    return false;
                }

                return true;

            },
            checkRestoreForm(){
                let pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                let email = String(this.restoreForm.email).toLowerCase()
                return pattern.test(email);
            },
            checkActivateForm(){

                this.activateForm.rules = [
                    false, false, false, false, false
                ];

                let regex = [
                    '',
                    RegExp('(?=.*[a-z])'),
                    RegExp('(?=.*[A-Z])'),
                    RegExp('(?=.*[0-9])'),
                    RegExp('[a-zA-Z0-9]{8,}')
                ];

                let password = this.activateForm.password;
                let confirm = this.activateForm.confirm;

                if (password === confirm && password.length > 0) {
                    this.activateForm.rules[0] = true;
                }

                // check the number
                if (regex[1].test(password)) {
                    this.activateForm.rules[1] = true;
                }

                if (regex[2].test(password)) {
                    this.activateForm.rules[2] = true;
                }

                if (regex[3].test(password)) {
                    this.activateForm.rules[3] = true;
                }
                if (regex[4].test(password)) {
                    this.activateForm.rules[4] = true;
                }

                return this.activateForm.rules.every(elem => elem === true);
            }
        }
    });

</script>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>