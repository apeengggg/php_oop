const toastInstance = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

const swalInstance = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary btn-confirm mr-1',
        cancelButton: 'btn btn-danger btn-cancel',
    },
    buttonsStyling: false
})

class CommonJS{
    swalError(message, callback) {
        swalInstance.fire({
            title: 'Oops!',
            text: message,
            icon: 'error',
            width: "400px",
            confirmButtonText: 'Ok'
        }).then(function () {
            if (callback) {
                callback()
            }
        })
    }

    swalConfirmAjax(message, confirmText, denyText, ajaxMethod, data, method, url, callback) {
        swalInstance.fire({
            title: 'Confirmation',
            html: message,
            width: "400px",
            confirmButtonText: confirmText,
            showCancelButton: true,
            closeOnConfirm: false,
            cancelButtonText: denyText,
            reverseButtons: false,
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading()
        }).then(async (result) => {
            if(result.isConfirmed){
                await ajaxMethod(url, data, method, callback)
            }
        })
    }

    async get(uri, success, error) {
        await $.ajax({
            url:baseUrl + uri,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
            },
            success: function (response) {
                if(response.status == 401){
                    commonJS.swalError(response.message, () => {
                        window.location.href = baseUrl
                    })
                }else if(response.status == 403){
                    commonJS.swalError(response.message, () => {
                        window.location.href = baseUrl + '/403'
                    })
                }else{
                    if(success){
                        success(response)
                    }
                }
            }
        });
    }

    async exec(uri, data, method, success, error){
        console.log("🚀 ~ CommonJS ~ exec ~ uri, data, method, success, error:", uri, data, method, success, error)
        await $.ajax({
            url: baseUrl + uri,
            type: method,
            data: data,
            dataType: 'JSON',
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
            },
            success: function (response) {
                if(response.status == 401){
                    commonJS.swalError(response.message, () => {
                        window.location.href = baseUrl
                    })
                }else if(response.status == 403){
                    commonJS.swalError(response.message, () => {
                        window.location.href = baseUrl + '/403'
                    })
                }else{
                    if(success){
                        success(response)
                    }
                }
            },
            error: function (exception){
                if(error){
                    error(exception)
                }
            }
        });
    }

    async execUpload(uri, data, method, success, error){
        await $.ajax({
            url: baseUrl + uri,
            type: method,
            data: data,
            contentType: false,
            processData: false,
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
            },
            success: function (response) {
                if(response.status == 401){
                    commonJS.swalError(response.message, () => {
                        window.location.href = baseUrl
                    })
                }else if(response.status == 403){
                    commonJS.swalError(response.message, () => {
                        window.location.href = baseUrl + '/403'
                    })
                }else{
                    if(success){
                        success(response)
                    }
                }
            },
            error: function (exception) {
                if(error){
                    error(exception)
                }
            }
        });
    }

    toast(msg, isError){
        toastInstance.fire({
            icon: isError ? 'error' : 'success',
            title: msg
        })
    }

    loading(stmn){
        if(stmn){
            $('#loading').show()
        }else{
            $('#loading').hide()
        }
    }
    
    logout(){
        this.exec('/logout', null, 'POST', () => {
            sessionStorage.clear()
            window.location.href = baseUrl
        })
    }

    setupPermission(function_id){
        console.log("🚀 ~ CommonJS ~ setupPermission ~ function_id:", function_id)
        var data = JSON.parse(sessionStorage.getItem("data"))
        var permission = data.permission
        var url = window.location.pathname.split("/")
        var len = url.length
        console.log("🚀 ~ CommonJS ~ setupPermission ~ url:", "/"+url[len-1])
        
        var master = []
        var transaction = []
        var dashboard = []
        var rows = "";

        var data = JSON.parse(sessionStorage.getItem('data'))

        $('#sideBarImage').attr('src', data.image)

        for(let i in  permission){
            if(permission[i].function_id.substring(0,1) === "D" && permission[i].can_read == "1"){
                dashboard.push(permission[i])
            }
            if(permission[i].function_id.substring(0,1) === "M" && permission[i].can_read == "1"){
                master.push(permission[i])
            }
            if(permission[i].function_id.substring(0,1) === "T" && permission[i].can_read == "1"){
                transaction.push(permission[i])
            }

            if(permission[i].function_id == function_id){
                $("#pageTitle").text(permission[i].function_name)
                $("#breadcrumbTitle").text(permission[i].function_name)

                if(permission[i].can_create == "0"){
                    $("#btnAdd").remove()
                }
                if(permission[i].can_update == "0"){
                    $('[id*="btnEdit"]').remove()
                }
                if(permission[i].can_delete == "0"){
                    $('[id*="btnDelete"]').remove()
                }
            }
        }

        if(dashboard.length > 0) {
            rows += `<li class="nav-header" id="#sideBarDashboard">Dashboard</li>`
            for(let j in dashboard){
                rows += `<li class="nav-item">`
                if("/"+url[len-1] === dashboard[j].url){
                    rows += `<a href="${baseUrl}${dashboard[j].url}" class="nav-link active">`
                }else{
                    rows += `<a href="${baseUrl}${dashboard[j].url}" class="nav-link">`
                }

                rows += `
                    <i class="nav-icon fas ${dashboard[j].icon}"></i>
                    <p>
                        ${dashboard[j].function_name}
                    </p>
                </a>
                </li>
                `
            }
        }

        if(master.length > 0) {
            rows += `<li class="nav-header" id="#sidebarMaster">Master</li>`
            for(let j in master){
                rows += `<li class="nav-item">`
                if("/"+url[len-1] === master[j].url){
                    rows += `<a href="${baseUrl}${master[j].url}" class="nav-link active">`
                }else{
                    rows += `<a href="${baseUrl}${master[j].url}" class="nav-link">`
                }

                rows += `
                    <i class="nav-icon fas ${master[j].icon}"></i>
                    <p>
                        ${master[j].function_name}
                    </p>
                </a>
                </li>
                `
            }
        }

        if(transaction.length > 0) {
            rows += `<li class="nav-header">Transaction</li>`
            for(let k in transaction){
                rows += `<li class="nav-item">`
                if("/"+url[len-1] === transaction[k].url){
                    rows += `<a href="${baseUrl}${transaction[k].url}" class="nav-link active">`
                }else{
                    rows += `<a href="${baseUrl}${transaction[k].url}" class="nav-link">`
                }

                rows += `
                    <i class="nav-icon fas ${transaction[k].icon}"></i>
                    <p>
                        ${transaction[k].function_name}
                    </p>
                </a>
                </li>
                `
            }
        }

        rows += `<li class="nav-header"> System </li>
            <li class="nav-item">
                <a onclick="commonJS.logout()" class="nav-link">
                    <i class="nav-icon fa-solid fa-right-from-bracket"></i>
                    <p>Logout</p>
                </a>
            </li>`

        $("#nav").html(rows)
    }
}

const commonJS = new CommonJS()