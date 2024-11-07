<?php include __DIR__. '../../templates/header.php' ?>
<?php include __DIR__. '../../templates/topnavbar.php' ?>
<?php include __DIR__. '../../templates/sidebar.php' ?>

<?php
$_SESSION['logged_in'] = true;
?>
<style>
  #eventData {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

#eventData .card {
    flex: 0 0 auto; /* Ukuran asli untuk semua card */
}

</style>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                     
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
                <div class="row my-3" id="listEvent">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Filter Event</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <input type="text" class="form-control" id="filterEventName" placeholder="Event Name">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <input type="text" class="form-control" id="filterLocation" placeholder="Location">
                                    </div>
                                    <div class="col-md-4 mb-1">
                                    <input type="text" readonly class="form-control event-datepicker" id="filterDate" placeholder="Date">
                                    </div>
                                </div>
                                <div class="row justify-content-end mt-2">
                                    <button class="btn btn-sm btn-danger mr-2" onclick="clearFilter()">Clear</button>
                                    <button class="btn btn-sm btn-primary" onclick="search(1)">Search</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="loading" style="display:none;">
                                    Loading ...
                                </div>
                                <div id="eventNotFound" style="display:none;">
                                    <h4>No Data</h4>
                                </div>
                                <div class="text-right mb-2">
                                    <button class="btn btn-sm btn-primary" id="btnAdd" onclick="add(1)">
                                    Add Event
                                    </button>
                                </div>
                                <div id="eventData">
                                </div>
                                <nav class="mt-2">
                                    <ul id="pagination" class="pagination justify-content-end"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include __DIR__. '/add.php' ?>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__. '../../templates/jquery.php' ?>
<script src="../public/jquery/event.js"></script>
<?php include __DIR__. '../../templates/footer.php' ?>