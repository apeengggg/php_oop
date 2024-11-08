var orderBy = 'user_id'
var initPage = 1;
var perPagePages = 10;
var dir = 'asc';
var isEdit = false;
var user_id = '';

$('#imageInput').change(function(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#profileImage').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
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

function editData(data){
    isEdit = true
    $('#userFormTitle').text("Edit User")
    $('#userName').val(data.name)
    $('#userUsername').val(data.username)
    $('#profileImage').attr('src', data.image)
    $('#userRole').val(data.role_id)
    $('#passwordForm').hide()
    user_id = data.user_id
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
    $('#filterName').val('')
    $('#filterUsername').val('')
    $('#filterRole').val('')
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
                <button type="button" class="page-link" onclick="search(${currentPage + 1})">Next</button>
            </li>
        `;
    }
}

function buildTemplate(index, data){
    console.log("🚀 ~ buildTemplate ~ data:", data)
    var rows = ""
    var string = JSON.stringify(data[index])
    var button = `
    <button id="btnEdit${index+1}" class='btn btn-sm btn-primary mb-1' onclick='editData(${string})'>
        <i class='fas fa-edit'></i>
    </button>
    <button id="btnDelete${index+1}" class='btn btn-sm btn-danger ml-2 mb-1' onclick='deleteData("${data[index].user_id}")'>
        <i class='fas fa-trash'></i>
    </button>
    `
    rows += '<tr class="template-data">'
        rows += '<td style="text-align: left;">' 
            rows += `<img src="${data[index].image}" alt="Profile Image" class="img-thumbnail" style="width: 50px; height: 50px; margin-right: 8px;">`
            rows += data[index].name
        rows += '</td>'
        rows += '<td>'+ data[index].username +'</td>'
        rows += '<td>'+ data[index].role_name +'</td>'
        rows += '<td>'+ button +'</td>'
    rows += '</tr>'

    return rows;
}

async function deleteData(user_id){
    commonJS.swalConfirmAjax('Are you sure want to delete this data?', 'Yes', 'No', commonJS.exec, {user_id: user_id} , 'POST', '/user/delete', (response)=> {
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

    if($('#filterName').val()){
        param += `&name=${$('#filterName').val()}`
    }

    if($('#filterUsername').val()){
        param += `&username=${$('#filterUsername').val()}`
    }

    if($('#filterRole').val()){
        param += `&role=${$('#filterRole').val()}`
    }

    $(".template-data").remove()
    $('#userNotFound').show()
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    await commonJS.get('/users/get'+param, async (response)=> {
        console.log("🚀 ~ commonJS.get ~ response:", response)
        if(response.status == 200){
            if(response.data.length > 0){
                $('#userNotFound').hide()
                var rows = ''

                rows = await Promise.all(
                    response.data.map((data, index) => buildTemplate(index, response.data))
                );

                await renderPagination(response.totalPages, page)

                console.log("🚀 ~ commonJS.get ~ rows:", rows)
                $("#userData>tbody").append(rows);
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
        commonJS.toast(error.toString(), true)
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
    await search(initPage)
    await commonJS.setupPermission("M001");
});