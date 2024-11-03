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
}

const commonJS = new CommonJS()