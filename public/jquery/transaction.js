var orderBy = 'event_booking_id'
var initPage = 1;
var perPagePages = 10;
var dir = 'asc';
var isEdit = false;
var event_booking_id = '';
var isUser = false

$('.event-datepicker').daterangepicker({
    singleDatePicker: false,
    showDropdowns: true,
    locale: {
        format: 'DD/MM/YYYY'
    }
});

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

function clearFilter(){
    $('#filterEventName').val('')
    $('#filterUsername').val('')
    $('#filterDate').val('')
    search(1)
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

    var date = moment(data[index].date).format('DD MMMM YYYY')
    var time = data[index].start_time.slice(0, 5) + " WIB";
    var present_badge = data[index].is_present == 1 ? 'success' : 'primary'
    var present = data[index].is_present == 1 ? 'Present' : 'Not Present'
    
    if(data[index].status_ticket == 2){
        present_badge = 'danger'
        present = 'Canceled'
    }

    var badge = `<span class="badge badge-${present_badge}">${present}</span>`

    var button = `
    <button id="btnDelete${index+1}" class='btn btn-sm btn-danger ml-2' onclick='deleteData("${data[index].event_booking_id}")'>
        <i class='fas fa-trash'></i>
    </button>
    `
    rows += '<tr class="template-data">'
    if(!isUser){
        rows += '<td>'
            rows += data[index].username
        rows += '</td>'
    }
    
    rows += '<td>'+ data[index].event_name +'</td>'
    rows += '<td>'+ date +'</td>'
    rows += '<td>'+ time +'</td>'
    rows += '<td>'+ badge +'</td>'
    rows += '<td>'+ button +'</td>'
    rows += '</tr>'

    return rows;
}

async function deleteData(event_booking_id){
    commonJS.swalConfirmAjax('Are you sure want to delete this data?', 'Yes', 'No', commonJS.exec, {event_booking_id: event_booking_id} , 'POST', '/transaction/delete', (response)=> {
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

    if($('#filterUsername').val()){
        param += `&username=${$('#filterUsername').val()}`
    }

    if($('#filterDate').val()){
        let date = $('#filterDate').val()
        date = date.split(' - ')

        date[0] = moment(date[0], 'DD/MM/YYYY').format('YYYY-MM-DD')
        date[1] = moment(date[1], 'DD/MM/YYYY').format('YYYY-MM-DD')

        param += `&date_start=${date[0]}&date_end=${date[1]}`
    }

    $(".template-data").remove()
    $('#transactionNotFound').show()
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    await commonJS.get('/transaction/get'+param, async (response)=> {
        console.log("🚀 ~ commonJS.get ~ response:", response)
        if(response.status == 200){
            if(response.data.length > 0){
                $('#transactionNotFound').hide()
                var rows = ''

                rows = await Promise.all(
                    response.data.map((data, index) => buildTemplate(index, response.data))
                );

                await renderPagination(response.totalPages, page)

                console.log("🚀 ~ commonJS.get ~ rows:", rows)
                $("#transactionData>tbody").append(rows);
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
        formData.append("event_booking_id", event_booking_id)
    }
    
    if($('#imageInput')[0].files[0]){
        // console.log("🚀 ~ save ~ $('#imageInput')[0].files[0]:", $('#imageInput')[0].files[0])
        formData.append("image", $('#imageInput')[0].files[0])
    }

    let url = isEdit ? '/transaction/put' : '/transaction/post'

    commonJS.swalConfirmAjax("Do you want to save this data?", "Yes", "No", commonJS.execUpload, formData, 'POST', url, async (response) => {
        console.log("🚀 ~ commonJS.execUpload ~ response:", response)
        if(response.status == 200){
            commonJS.toast(response.message, false)
            await search(1)
        }else{
            commonJS.toast(response.message, true)
        }
    })
}

function checkIsUser(){
    var data = JSON.parse(sessionStorage.getItem('data'))
    if(data.role_id === '2'){
        isUser = true
        $('#filterUsername').remove()
        $('#usernameColumn').remove()
    }
}

$(async function (){
    $('#filterDate').val('')
    checkIsUser()
    await commonJS.setupPermission("T001");
    await search(initPage)
});