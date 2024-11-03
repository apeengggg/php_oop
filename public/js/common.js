const toastInstance = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

class CommonJS{
    get(controller, success, error) {
        $.ajax({
            url: controller,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
            },
            success: function (response) {
                if(response.status == 200){
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

    exec(controller, data, method, success, error){
        $.ajax({
            url: controller,
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
                }
            },
            error: function (exception){
                if(error){
                    error(exception)
                }
            }
        });
    }

    execUpload(controller, data, method, success, error){
        $.ajax({
            url: controller,
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