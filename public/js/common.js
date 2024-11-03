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
            console.log("🚀 ~ CommonJS ~ swalConfirmAjax ~ result:", result)
            if(result.isConfirmed){
                await ajaxMethod(url, data, method, callback)
            }
        })
    }

    get(controller, success, error) {
        $.ajax({
            url: controller,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
            },
            success: function (response) {
            console.log("🚀 ~ CommonJS ~ get ~ response:", response)

                if(response.status == 200){
                    if(success){
                        success(response)
                    }
                }else if(response.status == 401){
                    commonJS.swalError(response.message, () => {
                        window.location.href = baseUrl
                    })
                }
            }
        });
    }

    exec(uri, data, method, success, error){
        console.log("🚀 ~ CommonJS ~ exec ~ uri, data, method, success, error:", uri, data, method, success, error)
        $.ajax({
            url: baseUrl + uri,
            type: method,
            data: data,
            dataType: 'JSON',
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
            },
            success: function (response) {
                if(response.status == 200){
                    if(success){
                        success(response)
                    }
                }else if(response.status == 401){
                    commonJS.swalError(response.message, () => {
                        window.location.href = baseUrl
                    })
                }
            },
            error: function (exception){
                if(error){
                    error(exception)
                }
            }
        });
    }

    execUpload(uri, data, method, success, error){
        $.ajax({
            url: baseUrl + uri,
            type: method,
            data: data,
            contentType: false,
            processData: false,
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
            },
            success: function (response) {
                if(response.status == 200){
                    if(success){
                        success(response)
                    }
                }else if(response.status == 401){
                    commonJS.swalError(response.message, () => {
                        window.location.href = baseUrl
                    })
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

    setupPermission(function_id){
        var data = JSON.parse(sessionStorage.getItem("data"))
        var permission = data.permission
        var master = []
        var url = window.location.pathname.split("/")
        var len = url.length
        console.log("🚀 ~ CommonJS ~ setupPermission ~ url:", "/"+url[len-1])
        
        var transaction = []
        var rows = "";

        for(let i in  permission){
            if(permission[i].function_id.substring(0,1) === "M" && permission[i].can_read === "1"){
                master.push(permission[i])
            }
            if(permission[i].function_id.substring(0,1) === "T" && permission[i].can_read === "1"){
                transaction.push(permission[i])
            }

            if(permission[i].function_id == function_id){
                if(permission[i].can_create === "0"){
                    $("#btnAdd").remove()
                }
                if(permission[i].can_edit === "0"){
                    $("#btnEdit").remove()
                }
                if(permission[i].can_delete === "0"){
                    $("#btnDelete").remove()
                }
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
                if(window.location.pathname === transaction[k].url){
                    rows += `<li class="nav-item active">`
                }else{
                    rows += `<li class="nav-item">`
                }

                rows += `
                <a href="${transaction[k].url}?>" class="nav-link">
                    <i class="nav-icon fas ${transaction[k].icon}"></i>
                    <p>
                        ${transaction[k].function_name}
                    </p>
                </a>
                </li>
                `
            }
        }

        console.log("🚀 ~ CommonJS ~ setupPermission ~ master:", master)
        console.log("🚀 ~ CommonJS ~ setupPermission ~ transaction:", transaction)
        console.log("🚀 ~ CommonJS ~ setupPermission ~ window.location.pathname:", window.location)

        $("#nav").html(rows)
        console.log("🚀 ~ CommonJS ~ setupPermission ~ data:", data)
    }
}

const commonJS = new CommonJS()