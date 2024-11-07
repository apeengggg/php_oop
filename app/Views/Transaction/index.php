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
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Filter Transaction</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <input type="text" readonly class="form-control event-datepicker" id="filterDate" placeholder="Event Date">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <input type="text" class="form-control" id="filterEventName" placeholder="Event Name">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <input type="text" class="form-control" id="filterUsername" placeholder="Username">
                                    </div>
                                </div>
                                <div class="row justify-content-end mt-2">
                                    <button class="btn btn-sm btn-danger mr-2" onclick="clearFilter()">Clear</button>
                                    <button class="btn btn-sm btn-primary" onclick="search(1)">Search</button>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table id="transactionData" class="table table-striped table-hover text-center" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th id="usernameColumn" onclick="orderDynamically('username')">
                                                Username
                                            </th>    
                                            <th id="eventNameColumn" onclick="orderDynamically('event_name')">
                                                Event Name
                                            </th>
                                            <th id="eventDateColumn" onclick="orderDynamically('date')">
                                                Event Date
                                            </th>
                                            <th id="eventTimeColumn" onclick="orderDynamically('start_time')">
                                                Event Time
                                            </th>
                                            <th id="eventPresentColumn" onclick="orderDynamically('status')">
                                                Status
                                            </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="transactionNotFound">
                                            <td colspan="6">Not Found</td>
                                        </tr>
                                        <tr id="loading">
                                            <td colspan="6">Loading</td>
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
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__. '../../templates/jquery.php' ?>
<script src="../public/jquery/transaction.js"></script>
<?php include __DIR__. '../../templates/footer.php' ?>