<div class="row my-3" id="addEvent">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h5 id="formTitle">Add Event</h5>
                    </div>
                    <div class="col-md-6 text-right" onclick="add(0)"><i class="fa-solid fa-xmark"></i></div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="eventName">Event Name</label>
                        <input type="text" class="form-control" id="eventName" placeholder="Event Name">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="eventDate">Date</label>
                        <input type="text" readonly class="form-control" id="eventDate" placeholder="Event Date">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="eventTime">Time</label>
                        <input type="time" class="form-control" id="eventTime">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="eventLocation">Location</label>
                        <textarea class="form-control" name="eventLocation" id="eventLocation"></textarea>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="eventDescription">Address</label>
                        <textarea class="form-control" name="eventDescription" id="eventDescription"></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="eventImage">Event Image</label>
                        <input type="file" class="form-control" id="eventImage">
                    </div>
                    <div class="form-group col-md-6 text-center">
                        <img src="../public/img/common_event.png" alt="" width="250" id="previewEventImage">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-right mt-2">
                    <button type="button" class="btn btn-danger">Clear</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>

            </div>
        </div>
    </div>
</div>