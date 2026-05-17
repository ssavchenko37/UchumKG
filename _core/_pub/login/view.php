<div class="col-sm-12 col-md-10 col-lg-6">
    <div class="card border-0">
        <div class="card-header bg-circle-shape bg-shape text-center p-2">
            <h4 class="z-1 position-relative link-light my-2">Курсы с Викторией Кринвальд</h4>
        </div>
        <div class="card-body p-4">
            <div class="row flex-between-center">
                <div class="col-auto">
                    <h3>Вход в личный кабинет</h3>
                </div>
            </div>
            <form method="post" action="/login/">
                <input type="hidden" name="login_mode" value="login">
                <div class="mb-3 my-3">
                    <label class="form-label" for="split-login-cell">Номер телефона</label>
                    <input class="form-control" id="split-login-cell" name="your_cell" type="text">
                </div>

                <div class="mb-3 mt-4"><button class="btn btn-primary btn-lg d-block w-100 mt-3" type="submit" name="submit">Войти</button></div>
            </form>
        </div>
    </div>
</div>