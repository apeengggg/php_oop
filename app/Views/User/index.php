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
                    <h1 class="m-0" id="pageTitle"></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" id="breadcrumbTitle"></li>
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
                        <div class="card">
                            <div class="card-header">
                                <h5>Filter User</h5>
                                <div class="row">
                                    <div class="col">
                                        <input type="text" class="form-control" id="filterName" placeholder="Name">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" id="filterUsername" placeholder="Username">
                                    </div>
                                    <div class="col">
                                    <select name="role" id="filterRole" class="form-control">
                                            <option value="">-- Choose Role --</option>
                                            <option value="1">Super Admin</option>
                                            <option value="2">User</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row justify-content-end mt-2">
                                    <button class="btn btn-sm btn-danger mr-2" onclick="clearFilter()">Clear</button>
                                    <button class="btn btn-sm btn-primary" onclick="search(1)">Search</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="userData" class="table table-striped table-hover text-center" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th onclick="orderDynamically('name')">Name</th>
                                            <th onclick="orderDynamically('username')">Username</th>
                                            <th onclick="orderDynamically('role_name')">Role</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="userNotFound">
                                            <td colspan="4">Not Found</td>
                                        </tr>
                                        <tr id="loading">
                                            <td colspan="4">Loading</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <nav class="mt-2">
                                    <ul id="pagination" class="pagination justify-content-end"></ul>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form action="POST" enctype="multipart/form-data">
                            <div class="card">
                                <h5 class="card-header" id="userFormTitle">Add User</h5>
                                <div class="card-body"id="formUser">
                                    <div class="form-group">
                                    <div class="alert alert-danger" role="alert" id="userFormErrorMessage" style="display:none;">
                                    </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <img id="profileImage" onclick="changeImage()" src="../public/img/common.png" alt="" width="100">
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
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select name="role" id="userRole" class="form-control">
                                            <option value="">-- Choose Role --</option>
                                            <option value="1">Super Admin</option>
                                            <option value="2">User</option>
                                        </select>
                                        <!-- <input type="text" class="form-control" id="userRole" placeholder="Username"> -->
                                    </div>
                                    <div class="form-group" id="passwordForm">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="userPassword" placeholder="Password">
                                    </div>
                                </div>
                                <div class="card-footer text-right" id="btnAdd">
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

<?php include __DIR__. '../../templates/jquery.php' ?>
<script src="../public/jquery/user.js"></script>
<?php include __DIR__. '../../templates/footer.php' ?>