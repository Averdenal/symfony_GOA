module.exports = function () {
    $('#form_New_PWD').submit(function (e) {
        debugger
        e.preventDefault();
        var password = $('#Password').val();
        if(password === $( '#Password2').val()){
            var data = { email:$('#Email').val(), password:password,id:$(this).data('id')};
             $.ajax({
                 type: "POST",
                 url: "/changepwd",
                 data: data,
                 success: function (response) {
                     console.log(response['changement']);
                    if(response['changement'] == 'ok'){
                        var info = $('#info_Change')
                        info.removeClass('none');
                        info.append("<p>"+response['changement']+", Votre mot de passe est changé</p>");
                        setInterval(function () {
                            location.replace('/login')
                        },2000)
                    }
                 }
             })
        }
    })
}