<?php include __DIR__. '../../templates/header.php' ?>
<?php include __DIR__. '../../templates/topnavbar.php' ?>
<?php include __DIR__. '../../templates/sidebar.php' ?>

<?php
$_SESSION['logged_in'] = true;
?>

<style>
  #upcomingEventData, #allEventData {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

#upcomingEventData .card, #allEventData .card {
    flex: 0 0 auto;
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
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header" style="display:none;" id="filterEvent">
                                <div class="row">
                                    <div class="col-6 text-left"><h5>Filter Event</h5></div>
                                    <div class="col-6 text-right" onclick="seeAll(0)"><i class="fa-solid fa-xmark"></i></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <input type="text" readonly class="form-control event-datepicker" id="filterDate" placeholder="Event Date">
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <input type="text" class="form-control" id="filterEventName" placeholder="Event Name">
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <input type="text" class="form-control" id="filterLocation" placeholder="Location">
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <select name="filterCategory" id="filterCategory" class="form-control">
                                            <option value="">-- Choose Category --</option>
                                            <option value="adwqjhw81723">Music</option>
                                            <option value="adw3qjhw81723">Drama</option>
                                            <option value="adwqjhw81724">StandUp Comedy</option>
                                            <option value="adwqjhw81729">Teater</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row justify-content-end mt-2">
                                    <button class="btn btn-sm btn-danger mr-2" onclick="clearFilter()">Clear</button>
                                    <button class="btn btn-sm btn-primary" onclick="search(1, 1)">Search</button>
                                </div>
                            </div>
                            <div class="card-body" id="upcomingEvent">
                                <div id="sectionUpcoming" class="mb-2">
                                    <div class="row">
                                        <div class="col-6 text-left"><h5>Upcoming Event</h5></div>
                                        <div class="col-6 text-right"><a href="#" onclick="seeAll(1)">See All</a></div>
                                    </div>
                                </div>
                                <div id="eventNotFound" class="text-center" style="display:none;">
                                    <h5>No Data</h5>
                                </div>
                                <div id="upcomingEventData" class="horizontal-scroll">
                                </div>
                            </div>
                            <div class="card-body" id="allEvent" style="display:none;">
                                <div id="eventNotFoundAll" class="text-center" style="display:none;">
                                    <h5>No Data</h5>
                                </div>
                                <div id="allEventData">
                                </div>
                                <nav class="mt-2">
                                    <ul id="pagination" class="pagination justify-content-end"></ul>
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
<script src="../public/jquery/dashboardUser.js"></script>
<?php include __DIR__. '../../templates/footer.php' ?>