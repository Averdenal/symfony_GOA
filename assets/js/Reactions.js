module.exports = function() {
    $('.btn_Action_Reaction').on('click',function () {
        let id = $(this).parent().data('id');
        $('#zone_Action_User_Post_'+id).addClass('none');
        $('#zone_Reaction_Post_'+id).removeClass('none');
    });
    $('.btn_Action_Close_Reaction').on('click',function () {
        let id = $(this).parent().data('id');
        $('#zone_Action_User_Post_'+id).removeClass('none');
        $('#zone_Reaction_Post_'+id).addClass('none');
    });
    $('.action_Reaction').on('click',function (e) {
        e.preventDefault();
        $.post("/add/Reaction",
               {
                   reaction: $(this).data('id'),
                   post: $(this).parent().data('id')
               })
        .always(function() {
                location.reload();
            });
    });
    $('.delete_Post_User').on('click',function (e) {
        e.preventDefault();
        $.ajax({
                   url : "/delete/post/"+$(this).parent().data('id'),
                   type : 'DELETE'
               })

        .always(function(response) {
                    location.replace("/profil/show/"+response);
                });
    });
    $('.delete_Comment_User').on('click',function (e) {
        e.preventDefault();
        $.ajax({
                   url : "/delete/comment/"+$(this).parent().data('id'),
                   type : 'DELETE'
               })

            .always(function() {
                location.reload();
            });
    });
}