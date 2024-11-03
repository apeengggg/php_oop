<?php include __DIR__. '../../templates/header.php' ?>
<?php include __DIR__. '../../templates/topnavbar.php' ?>
<?php include __DIR__. '../../templates/sidebar.php' ?>

<?php
$_SESSION['logged_in'] = true;
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Master User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Master User</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="container mt-4">
                <div class="row my-3">
                    <div class="col-md-8">
                        <table id="userData" class="table table-striped table-hover text-center" style="width:100%">
                            <thead>
                                <tr>
                                    <th onclick="orderDynamically('name')">Name</th>
                                    <th onclick="orderDynamically('username')">Username</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="userNotFound">
                                    <td colspan="3">Not Found</td>
                                </tr>
                                <tr id="loading">
                                    <td colspan="3">Loading</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <form action="POST" enctype="multipart/form-data">
                            <div class="card">
                                <h5 class="card-header" id="userFormTitle">Add User</h5>
                                <div class="card-body"id="formUser">
                                    <div class="form-group text-center">
                                        <img id="profileImage" onclick="changeImage()" src="../../public/img/common.png" alt="" width="100">
                                        <input type="file" id="imageInput" accept="image/*" style="display: none;">
                                    </div>
                                    <div class="form-group">
                                        <small>Click on the image to change it</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="userName" placeholder="Name">
                                    </div>    
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="userUsername" placeholder="Username">
                                    </div>
                                    <div class="form-group" id="passwordForm">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="userPassword" placeholder="Password">
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="button" onclick="clearUserForm()" class="btn btn-danger">Clear</button>
                                    <button type="button" onclick="save()" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include __DIR__. '../../templates/footer.php' ?>