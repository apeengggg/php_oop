$('#password').on('keypress', function (e) {
    console.log("🚀 ~ e:", e)
    if (e.key === 'Enter') {
        login()
    }
});

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
            let result = response
            if(result.status == 200){
                sessionStorage.setItem('token', result.token)
                sessionStorage.setItem('data', JSON.stringify(result.data))
                window.location.href = 'index.php/dashboard'
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