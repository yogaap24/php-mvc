<div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <?php if (isset($data['error'])) : ?>
        <div class="row">
            <div class="alert alert-danger" role="alert">
                <?= $data['error'] ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($data['success'])) : ?>
        <div class="row">
            <div class="alert alert-success" role="alert">
                <?= $data['success'] ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="row align-items-center g-lg-5 py-5">
        <div class="col-lg-7 text-center text-lg-start">
            <h1 class="display-4 fw-bold lh-1 mb-3">Profile</h1>
            <p class="col-lg-10 fs-4">by <a target="_blank" href="https://yoo.ga/">Yogaap</a></p>
            <a href="/home/dashboard">Back</a>
        </div>
        <div class="col-md-10 mx-auto col-lg-5">
            <form class="p-4 p-md-5 border rounded-3 bg-light" method="post" action="/users/profile/<?= $data['id'] ?? '' ?>">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="id" placeholder="id" value="<?= $data['id'] ?? '' ?>" disabled>
                    <label for="id">Id</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="email" type="text" class="form-control" id="email" placeholder="email" value="<?= $data['email'] ?? '' ?>">
                    <label for="email">Email</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</div>