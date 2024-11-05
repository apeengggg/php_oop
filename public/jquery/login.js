function login(){
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
        url: 'index.php/login/authenticate',
        type: 'POST',
        data: data,
        success: function (response) {
        console.log("🚀 ~ login ~ response:", response)
            let result = JSON.parse(response) || {status: 400, message: 'Bad Request'}
            if(result.status == 200){
                sessionStorage.setItem('token', result.token)
                sessionStorage.setItem('data', JSON.stringify(result.data))
                if(result.data.role_id == '2'){
                    window.location.href = 'index.php/transactions'
                }else{
                    window.location.href = 'index.php/users'
                }
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
    sessionStorage.clear()
})