function login(){
    event.preventDefault()
    $('#loginErrorMessage').hide()
    const username = $('#username').val();
    const password = $('#password').val();

    let error = []

    if(username == ''){
        error.push('Username Required')
    }
    
    if(password == ''){
        error.push('Password Required')
    }
    
    if(error.length > 0){
        $('#loginErrorMessage').text(error.toString()).show();
        return
    }

    let data = {
        username: username,
        password: password
    }

    $.ajax({
        url: 'app/Controllers/AuthController.php',
        type: 'POST',
        data: data,
        success: function (response) {
            let result = JSON.parse(response) || {status: 400, message: 'Bad Request'}
            if(result.status == 200){
                sessionStorage.setItem('token', result.token)
                sessionStorage.setItem('data', JSON.stringify(result.data))
                window.location.href = 'index.php/admin/dashboard'
            }else{
                commonJS.toast(result.message, true)
            }
        },
        error: function (response) {
            console.error(response)
        }
    });
}

$(function(){
    // commonJS.alertShow()
})