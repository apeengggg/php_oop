var orderBy = 'user_id'
var initPage = 1;
var perPagePages = 10;
var dir = 'asc';
var isEdit = false;

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
    $('#userRole').val(data.role_id)
    $('#passwordForm').hide()
}

function clearUserForm(){
    isEdit = false
    var title = isEdit ? "Edit User" : "Add User"
    $('#userFormTitle').text(title)
    $('#userName').val('')
    $('#userUsername').val('')
    $('#userRole').val('')
    $('#passwordForm').show()
    $('#profileImage').attr('src', '../../public/img/common.png');
}

function changeImage(){
    $('#imageInput').click();
}

function buildTemplate(index, data){
    var rows = ""
    var string = JSON.stringify(data[index])
    var button = `
    <button id="btnEdit" class='btn btn-sm btn-primary' onclick='editData(${string})'>
        <i class='fas fa-edit'></i>
    </button>
    <button id="btnDelete" class='btn btn-sm btn-danger ml-2' onclick='deleteData("${data[index].user_id}")'>
        <i class='fas fa-trash'></i>
    </button>
    `
    rows += '<tr class="template-data">'
        rows += '<td>'+ data[index].name +'</td>'
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

    if($('#usernameFilter').val()){
        param += `&username=${$('#usernameFilter').val()}`
    }

    if($('#name').val()){
        param += `&name=${$('#name').val()}`
    }

    var url = baseUrl + '/users/get'
    $(".template-data").remove()
    commonJS.get(url+param, async (response)=> {
        // console.log("🚀 ~ commonJS.get ~ response:", response)
        if(response.status == 200){
            if(response.data.length > 0){
                $('#userNotFound').hide()
                var rows = ''

                for(var index in response.data){
                    // console.log("🚀 ~ commonJS.get ~ index:", index)
                    rows += buildTemplate(index, response.data)
                }

                // console.log("🚀 ~ commonJS.get ~ rows:", rows)
                $("#userData>tbody").append(rows);

            }
        }
    }, (error) => {
        console.error(error)
    })

    commonJS.loading(false)
}

$(function(){
    commonJS.setupPermission("M001");
    search(initPage)
});