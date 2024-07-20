<div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <?php if (isset($data['error'])) : ?>
        <div class="row">
            <div class="alert alert-danger" role="alert">
                <?= $data['error'] ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="row align-items-center g-lg-5 py-5">
        <div class="col-lg-7 text-center text-lg-start">
            <h1 class="display-4 fw-bold lh-1 mb-3">Register</h1>
            <p class="col-lg-10 fs-4">by <a target="_blank" href="https://yoo.ga">Yogaap</a></p>
        </div>
        <div class="col-md-10 mx-auto col-lg-5">
            <form class="p-4 p-md-5 border rounded-3 bg-light" method="post" action="/users/register">
                <div class="form-floating mb-3">
                    <input name="email" type="text" class="form-control" id="email" placeholder="email" value="<?= $_POST["email"] ?? "" ?>">
                    <label for="email">Email</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="password" type="password" class="form-control" id="password" placeholder="password">
                    <label for="password">Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="confirm_password" type="password" class="form-control" id="confirm_password" placeholder="confirm password">
                    <label for="confirm_password">Confirm Password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Register</button>
            </form>
        </div>
    </div>
</div>