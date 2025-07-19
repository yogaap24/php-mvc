<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Profile</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="avatar mb-3">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                👤
                            </div>
                        </div>
                        <h5><?= Core\View\View::escape($user['email'] ?? 'User') ?></h5>
                        <p class="text-muted">Member since <?= date('Y') ?></p>
                    </div>
                    <div class="col-md-8">
                        <form method="POST" action="<?= Core\View\View::url('/users/profile') ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= Core\View\View::escape($user['email'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="created_at" class="form-label">Member Since</label>
                                <input type="text" class="form-control" value="<?= date('F j, Y') ?>" disabled>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h6>Quick Actions</h6>
                        <div class="btn-group" role="group">
                            <a href="<?= Core\View\View::url('/users/password') ?>" class="btn btn-outline-primary">Change Password</a>
                            <a href="<?= Core\View\View::url('/home/dashboard') ?>" class="btn btn-outline-secondary">Dashboard</a>
                            <a href="<?= Core\View\View::url('/users/logout') ?>" class="btn btn-outline-danger">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>