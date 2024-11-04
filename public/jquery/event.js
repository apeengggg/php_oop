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
    $("#eventName").val('')
    $("#eventDate").val('')
    $("#eventTime").val('')
    $("#eventLocation").val('')
    $("#eventDescription").val('')
    $("#eventImage").val('')
    $("#previewEventImage").attr('src', '../public/img/common_event.png');
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
    isEdit = true

    add(1)

    let date = moment(data.date).format('DD/MM/YYYY')

    $('#formTitle').text("Edit User")
    $('#eventName').val(data.event_name)
    $('#eventDate').val(date)
    $('#eventTime').val(data.start_time)
    $('#eventLocation').val(data.location)
    $('#eventDescription').val(data.description)
    $('#eventPreviewImage').attr('src', data.image)
    event_id = data.event_id
}

function clearUserForm(){
    isEdit = false
    var title = isEdit ? "Edit User" : "Add User"
    $('#userFormTitle').text(title)
    $('#userName').val('')
    $('#userUsername').val('')
    $('#userRole').val('')
    $('#userPassword').val('')
    $('#imageInput').val('')
    $('#passwordForm').show()
    $('#profileImage').attr('src', '../public/img/common.png');
    user_id = ''
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
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Date: ${date}</li>
                <li class="list-group-item">Time: ${time}</li>
                <li class="list-group-item">${data[index].location}</li>
            </ul>
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

    console.log("🚀 ~ search ~ $('#filterDate').val():", $('#filterDate').val())
    if($('#filterDate').val()){
        let date = $('#filterDate').val()
        date = date.split('-')
        date[0] = date[0].replace(/ /g,'')
        date[1] = date[1].replace(/ /g,'')

        date[0] = moment(date[0]).format('YYYY-MM-DD')
        date[1] = moment(date[1]).format('YYYY-MM-DD')

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
    var name = $('#userName').val()
    var username = $('#userUsername').val()
    var role = $('#userRole').val()
    var password = $('#userPassword').val()

    var error = []
    if(name == ''){
        error.push("Name Required")
    }

    if(username == ''){
        error.push("Username Required")
    }

    if(role == ''){
        error.push("Role Required")
    }

    if(password == '' && !isEdit){
        error.push("Password Required")
    }

    if(error.length > 0){
        $('#userFormErrorMessage').text(error.toString()).show();
        return;
    }

    var formData = new FormData()
    formData.append("name", name)
    formData.append("username", username)
    formData.append("role", role)
    if(!isEdit){
        formData.append("password", password)
    }else{
        formData.append("user_id", user_id)
    }
    
    if($('#imageInput')[0].files[0]){
        // console.log("🚀 ~ save ~ $('#imageInput')[0].files[0]:", $('#imageInput')[0].files[0])
        formData.append("image", $('#imageInput')[0].files[0])
    }

    let url = isEdit ? '/users/put' : '/users/post'

    commonJS.swalConfirmAjax("Do you want to save this data?", "Yes", "No", commonJS.execUpload, formData, 'POST', url, async (response) => {
        console.log("🚀 ~ commonJS.execUpload ~ response:", response)
        if(response.status == 200){
            commonJS.toast(response.message, false)
            await clearUserForm()
            await search(1)
        }else{
            commonJS.toast(response.message, true)
        }
    })

}

$(async function (){
    $('.event-datepicker').val('');
    $('#eventDate').val('');
    await commonJS.setupPermission("M003");
    await search(initPage)
});