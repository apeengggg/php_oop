var orderBy = 'event_id'
var initPage = 1;
var perPagePages = 3;
var dir = 'asc';
var isEdit = false;
var event_id = '';

$('.event-datepicker').daterangepicker({
    singleDatePicker: false,
    showDropdowns: true,
    locale: {
        format: 'DD/MM/YYYY'
    }
});

$('#eventDate').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    minDate: moment(),
    locale: {
        format: 'DD/MM/YYYY',
    }
});

$('#eventImage').change(function(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#previewEventImage').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    }
});

function add(isAdd){
    if(!isAdd){
        $("#listEvent").show()
        $("#addEvent").hide()
    }else{
        $("#listEvent").hide()
        $("#addEvent").show()
    }
    $('#formTitle').text('Add Event')
    clearEventForm()
}

async function orderDynamically(value){
    orderBy = value
    dir = dir == 'asc' ? 'desc' : 'asc';
    await search(1)
}

async function changePage(page){
    await search(page)
}

async function changePerPage(perPage){
    perPagePages = perPage 
    await search(1)
}

function editData(data){
    add(1)
    isEdit = true

    let date = moment(data.date).format('DD/MM/YYYY')

    $('#formTitle').text("Edit Event")
    $('#eventName').val(data.event_name)
    $('#eventDate').val(date)
    $('#eventTime').val(data.start_time)
    $('#eventLocation').val(data.location)
    $('#eventDescription').val(data.description)
    $('#availableTicket').val(data.available_ticket)
    $('#availableTicket').attr('min', data.available_ticket)
    $('#previewEventImage').attr('src', data.image)
    event_id = data.event_id
}

function clearEventForm(){
    isEdit = false
    var title = isEdit ? "Edit Event" : "Add Event"
    $('#titleForm').text(title)
    $('#eventName').val('')
    $('#eventDate').val('')
    $('#eventTime').val('')
    $('#eventLocation').val('')
    $('#eventDescription').val('')
    $('#previewEventImage').attr('src', '../public/img/common_event.png')
    $('#eventImage').val('');
    event_id = ''
}

function clearFilter(){
    $('#filterEventName').val('')
    $('#filterLocation').val('')
    $('#filterDate').val('')
    search(1)
}

function changeImage(){
    $('#imageInput').click();
}

function renderPagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    if (currentPage > 1) {
        pagination.innerHTML += `
            <li class="page-item">
                <button type="button" class="page-link" onclick="search(${currentPage - 1})">Prev</button>
            </li>
        `;
    }

    let startPage = Math.max(1, currentPage - 1);
    let endPage = Math.min(totalPages, currentPage + 1);

    if (currentPage === 1) {
        endPage = Math.min(totalPages, currentPage + 2);
    } else if (currentPage === totalPages) {
        startPage = Math.max(1, currentPage - 2);
    }

    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === currentPage ? 'active' : '';
        pagination.innerHTML += `
            <li class="page-item ${activeClass}">
                <button type="button" class="page-link" onclick="search(${i})">${i}</button>
            </li>
        `;
    }

    if (currentPage < totalPages) {
        pagination.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="search(${currentPage + 1})">Next</a>
            </li>
        `;
    }
}

function buildTemplate(index, data){
    var rows = ""
    var string = JSON.stringify(data[index])

    let date = moment(data[index].date).format('DD MMMM YYYY')
    let time = data[index].start_time.slice(0, 5) + " WIB";

    var button = `
    <button id="btnEdit${index+1}" class='btn btn-sm btn-primary' onclick='editData(${string})'>
        <i class='fas fa-edit'></i>
    </button>
    <button id="btnDelete${index+1}" class='btn btn-sm btn-danger ml-2' onclick='deleteData("${data[index].event_id}")'>
        <i class='fas fa-trash'></i>
    </button>
    `
    rows += `
        <div class="card template-data" style="width: 18rem;">
            <img src="${data[index].image}" class="card-img-top" alt="${data[index].event_name}">
            <div class="card-body">
                <h4>${data[index].event_name}</h4>
                <p class="card-text">${data[index].description}</p>
                <hr>
                <p class="card-text">Total Ticket: ${data[index].total_ticket}</p>
                <p class="card-text">Available Ticket: ${data[index].available_ticket}</p>
                <hr>
                <p class="card-text">Date: ${date}</p>
                <p class="card-text">Time: ${time}</p>
                <p class="card-text">${data[index].location}</p>
            </div>
            <div class="card-body text-right">
                ${button}
            </div>
        </div>
    `

    return rows;
}

async function deleteData(event_id){
    commonJS.swalConfirmAjax('Are you sure want to delete this data?', 'Yes', 'No', commonJS.exec, {event_id: event_id} , 'POST', '/event/delete', (response)=> {
        if(response.status == 200){
            commonJS.toast(response.message, false)
            search(1)
        }else{
            commonJS.toast(response.message, true)
        }
    })
}

async function search(page){
    commonJS.loading(true)
    let param = `?orderBy=${orderBy}&dir=${dir}&page=${page}&perPage=${perPagePages}`;

    if($('#filterEventName').val()){
        param += `&event_name=${$('#filterEventName').val()}`
    }

    if($('#filterLocation').val()){
        param += `&location=${$('#filterLocation').val()}`
    }

    if($('#filterDate').val()){
        let date = $('#filterDate').val()
        date = date.split(' - ')
        console.log("🚀 ~ search ~ date1:", date)

        date[0] = moment(date[0], 'DD/MM/YYYY').format('YYYY-MM-DD')
        date[1] = moment(date[1], 'DD/MM/YYYY').format('YYYY-MM-DD')
        console.log("🚀 ~ search ~ date:", date)


        param += `&date_start=${date[0]}&date_end=${date[1]}`
    }

    $(".template-data").remove()
    $('#eventNotFound').show()
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    await commonJS.get('/event/get'+param, async (response)=> {
        console.log("🚀 ~ commonJS.get ~ response:", response)
        if(response.status == 200){
            if(response.data.length > 0){
                $('#eventNotFound').hide()
                var rows = ''

                rows = await Promise.all(
                    response.data.map((data, index) => buildTemplate(index, response.data))
                );

                await renderPagination(response.totalPages, page)

                console.log("🚀 ~ commonJS.get ~ rows:", rows)
                $("#eventData").append(rows);
            }
        }else{
            commonJS.toast(response.message, true)
        }
    }, (error) => {
        console.error(error)
    })
    commonJS.loading(false)
}

async function save(){
    $('#userFormErrorMessage').hide();
    var eventName = $('#eventName').val()
    var eventDate = $('#eventDate').val()
    var eventTime = $('#eventTime').val()
    var eventLocation = $('#eventLocation').val()
    var eventDescription = $('#eventDescription').val()
    var availableTicket = $('#availableTicket').val()

    var error = []
    if(eventName == ''){
        error.push("Event Name Required")
    }

    if(eventDate == ''){
        error.push("Event Date Required")
    }

    if(eventTime == ''){
        error.push("Event Time Required")
    }

    if(eventLocation == ''){
        error.push("Event Location Required")
    }
    if(eventDescription == ''){
        error.push("Event Description Required")
    }

    if(availableTicket == ''){
        error.push("Available Ticket Required")
    }

    if(availableTicket <= 0){
        error.push("Available Must Be Greater Than 0")
    }

    if(error.length > 0){
        $('#eventErrorForm').text(error.toString()).show();
        return;
    }

    var formData = new FormData()
    formData.append("eventName", eventName)
    formData.append("eventDate", moment(eventDate, 'DD/MM/YYYY').format('YYYY-MM-DD'))
    formData.append("eventTime", eventTime)
    formData.append("eventLocation", eventLocation)
    formData.append("eventDescription", eventDescription)
    formData.append("availableTicket", availableTicket)
    if(isEdit){
        formData.append("event_id", event_id)
    }
    
    if($('#eventImage')[0].files[0]){
        formData.append("image", $('#eventImage')[0].files[0])
    }

    let url = isEdit ? '/event/put' : '/event/post'

    commonJS.swalConfirmAjax("Do you want to save this data?", "Yes", "No", commonJS.execUpload, formData, 'POST', url, async (response) => {
        console.log("🚀 ~ commonJS.execUpload ~ response:", response)
        if(response.status == 200){
            isEdit = false
            commonJS.toast(response.message, false)
            await add(0)
            await search(1)
        }else{
            commonJS.toast(response.message, true)
        }
    })

}

$(async function (){
    $('.event-datepicker').val('');
    $('#eventDate').val('');
    await search(initPage)
    await commonJS.setupPermission("M003");
});