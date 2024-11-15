<div class="row my-3" id="addEvent" style="display: none;">
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
                    <div class="form-group col-md-4">
                        <label for="eventName">Event Name</label>
                        <input type="text" class="form-control" id="eventName" placeholder="Event Name">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="eventDate">Date</label>
                        <input type="text" readonly class="form-control" id="eventDate" placeholder="Event Date">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="availableTicket">Available Ticket</label>
                        <input type="number" min="0" class="form-control" id="availableTicket" placeholder="Available Name">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="eventCategory">Category</label>
                        <select name="eventCategory" id="eventCategory" class="form-control">
                            <option value="">-- Choose Category --</option>
                            <option value="adwqjhw81723">Music</option>
                            <option value="adw3qjhw81723">Drama</option>
                            <option value="adwqjhw81724">StandUp Comedy</option>
                            <option value="adwqjhw81729">Teater</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="eventLocation">Location</label>
                        <textarea class="form-control" name="eventLocation" id="eventLocation"></textarea>
                    </div>
                    <div class="form-group col-md-4">
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
            <div class="card-footer" id="btnAdd">
                <div class="text-right mt-2">
                    <button type="button" class="btn btn-danger" onclick="clearEventForm()">Clear</button>
                    <button type="button" class="btn btn-primary" onclick="save()">Save</button>
                </div>

            </div>
        </div>
    </div>
</div>