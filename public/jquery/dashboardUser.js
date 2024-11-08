var orderBy = 'date'
var initPage = 1;
var perPagePages = 6;
var dir = 'asc';
var isEdit = false;
var event_id = '';
var isAll = false;

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

async function changePage(page){
    await search(page, 1)
}

async function changePerPage(perPage){
    perPagePages = perPage 
    await search(1, 1)
}

function clearFilter(){
    $('#filterEventName').val('')
    $('#filterLocation').val('')
    $('#filterDate').val('')
    $('#filterCategory').val('')
    search(1, 1)
}

function renderPagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    if (currentPage > 1) {
        pagination.innerHTML += `
            <li class="page-item">
                <button type="button" class="page-link" onclick="search(${currentPage - 1}, 1)">Prev</button>
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
                <button type="button" class="page-link" onclick="search(${i}, 1)">${i}</button>
            </li>
        `;
    }

    if (currentPage < totalPages) {
        pagination.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="search(${currentPage + 1}, 1)">Next</a>
            </li>
        `;
    }
}

async function bookingEvent(event_id){
    commonJS.swalConfirmAjax('Are you sure want to booking ticket for this event?', 'Yes', 'No', commonJS.exec, {event_id: event_id} , 'POST', '/event/booking', (response)=> {
        if(response.status == 200){
            commonJS.toast(response.message, false)
            seeAll(isAll)
        }else{
            commonJS.toast(response.message, true)
        }
    })
}

function buildTemplate(index, data){
    var rows = ""

    let date = moment(data[index].date).format('DD MMMM YYYY')

    let date_event = moment(data[index].date, 'YYYY-MM-DD')
    let pastDate = date_event.isBefore(moment().startOf('day'))
    
    let available_ticket = data[index].available_ticket <= 0 ? '<span class="text-danger">Ticket Sold Out</span>' : data[index].available_ticket

    let button = ` <div class="card-footer text-right">
                <button class="btn btn-primary" onclick="bookingEvent('${data[index].event_id}')">Booking</button>
            </div>`

    if(pastDate){
        button = ''
    }
    
    rows += `
        <div class="card template-data" style="width: 18rem;">
            <img src="${data[index].image}" class="card-img-top" height="300" alt="${data[index].event_name}">
            <div class="card-body">
                <h4>${data[index].event_name}</h4>
                <p class="card-text">${data[index].description}</p>
                <hr>
                <p class="card-text">Category: ${data[index].category_name}</p>
                <p class="card-text">Total Ticket: ${data[index].total_ticket}</p>
                <p class="card-text">Available Ticket: <b>${available_ticket}</b></p>
                <hr>
                <p class="card-text">Date: ${date}</p>
                <p class="card-text">${data[index].location}</p>
            </div>
            ${button}
        </div>
    `

    return rows;
}

function seeAll(seeAll){
    if(seeAll){
        $('#upcomingEvent').hide();
        $('#filterEvent').show();
        $('#allEvent').show();
        perPagePages = 3
        isAll = true
        search(1, 1)
    }else{
        $('#upcomingEvent').show();
        $('#filterEvent').hide();
        $('#allEvent').hide(); 
        isAll = false
        perPagePages = 6
        search(1, 0)
    }

}

async function search(page, seeAll = 0){
    commonJS.loading(true)
    let param = `?`;
    if(seeAll){
        param += `orderBy=${orderBy}&dir=${dir}&page=${page}&perPage=${perPagePages}`;
    }

    if($('#filterEventName').val()){
        param += `&event_name=${$('#filterEventName').val()}`
    }

    if($('#filterLocation').val()){
        param += `&location=${$('#filterLocation').val()}`
    }

    if($('#filterCategory').val()){
        param += `&category=${$('#filterCategory').val()}`
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
    $('#eventNotFoundAll').show()
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    await commonJS.get('/dashboard-user/get'+param, async (response)=> {
        console.log("🚀 ~ commonJS.get ~ response:", response)
        if(response.status == 200){
            if(response.data.length > 0){
                $('#eventNotFound').hide()
                $('#eventNotFoundAll').hide()
                var rows = ''

                rows = await Promise.all(
                    response.data.map((data, index) => buildTemplate(index, response.data))
                );

                if(seeAll){
                    $("#allEventData").append(rows);
                    await renderPagination(response.totalPages, page)
                }else{
                    $("#upcomingEventData").append(rows);
                }
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
        commonJS.toast(error.toString(), true)
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
            await search(1,1)
        }else{
            commonJS.toast(response.message, true)
        }
    })

}

$(async function (){
    $('.event-datepicker').val('');
    $('#eventDate').val('');
    await search(initPage)
    await commonJS.setupPermission("D001");
});