class Reaction {
    constructor() {
        this.addReaction();
        this.afficheReaction();
    }
    afficheReaction(){
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
    }
    addReaction(){
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
    }
}

module.exports = Reaction;