const toastInstance = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

class CommonJS{
    get(controller, data, method, success, error){
        $.ajax({
            url: controller,
            type: method,
            data: data,
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
            },
            success: function (response) {
                console.log("🚀 ~ CommonJS ~ get ~ response:", response)
            },
            error: function (jqXHR, textStatus, errorThrown) {
                error()
            }
        });
    }

    exec(controller, data, method, success, error){
        $.ajax({
            url: controller,
            type: method,
            data: data,
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
            },
            success: function (response) {
                return response.json()
            },
            error: function (response) {
                return JSON.parse(response)
            }
        });
    }

    toast(msg, isError){
        toastInstance.fire({
            icon: isError ? 'error' : 'success',
            title: msg
        })
    }
}

const commonJS = new CommonJS()