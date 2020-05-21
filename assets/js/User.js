class User{

    constructor() {
        this.deletePost();
        this.deleteComment();
        this.editUser();
        this.addComment();

        this.aNews = $('#sub_Menu_Page_News');
        this.sectionNews = $('#section_News');

        this.aInfos = $('#sub_Menu_Page_Infos');
        this.sectionInfos = $('#section_Infos');

        this.aFriends = $('#sub_Menu_Page_friends');
        this.sectionFriends = $('#section_Friends');

        this.aPhotos = $('#sub_Menu_Page_Photos');
        this.sectionPhotos = $('#section_Pictures');

        this.aGroups = $('#sub_Menu_Page_Groups');
        this.sectionGroups = $('#section_Groups');

        this.aEvents = $('#sub_Menu_Page_Events');
        this.sectionEvents = $('#section_Events');

        this.viewNews();
        this.viewInfos();
        this.viewFriends();
        this.viewPhotos();
        this.viewGroups();
        this.viewEvents();
    }
    editUser(){
        $('#btn_Edit_Section_Infos').click(function (e) {
            e.preventDefault();
            $('#edit_Section_Infos').removeClass('none');
            $('#edit_Section_Link').addClass('none');

            $(this).addClass('actif');
            $('#btn_Edit_Section_Link').removeClass('actif');
        });

        $('#btn_Edit_Section_Link').click(function (e) {
            e.preventDefault();
            $('#edit_Section_Infos').addClass('none');
            $('#edit_Section_Link').removeClass('none');

            $('#btn_Edit_Section_Infos').removeClass('actif');
            $(this).addClass('actif');
        });
    }

    viewNews(){
        this.aNews.click((e) =>{
            e.preventDefault();
            this.allNone();
            this.aNews.addClass('actif');
            this.sectionNews.removeClass('none');
        })
    }
    viewInfos(){
        this.aInfos.click((e) =>{
            e.preventDefault();
            this.allNone();
            this.aInfos.addClass('actif');
            this.sectionInfos.removeClass('none');
        })
    }
    viewFriends(){
        this.aFriends.click((e) =>{
            e.preventDefault();
            this.allNone();
            this.aFriends.addClass('actif');
            this.sectionFriends.removeClass('none');
        })
    }
    viewPhotos(){
        this.aPhotos.click((e) =>{
            e.preventDefault();
            this.allNone();
            this.aPhotos.addClass('actif');
            this.sectionPhotos.removeClass('none');
        })
    }
    viewGroups(){
        this.aGroups.click((e) =>{
            e.preventDefault();
            this.allNone();
            this.aGroups.addClass('actif');
            this.sectionGroups.removeClass('none');
        })
    }
    viewEvents(){
        this.aEvents.click((e) =>{
            e.preventDefault();
            this.allNone();
            this.aEvents.addClass('actif');
            this.sectionEvents.removeClass('none');
        })
    }
    allNone(){
        this.sectionNews.addClass('none');
        this.aNews.removeClass('actif');

        this.sectionInfos.addClass('none');
        this.aInfos.removeClass('actif');

        this.sectionFriends.addClass('none');
        this.aFriends.removeClass('actif');

        this.sectionPhotos.addClass('none');
        this.aPhotos.removeClass('actif');

        this.sectionGroups.addClass('none');
        this.aGroups.removeClass('actif');

        this.sectionEvents.addClass('none');
        this.aEvents.removeClass('actif');
    }
    deletePost(){
        $('.delete_Post_User').on('click',function (e) {
            e.preventDefault();
            $.ajax({
                       url : "/delete/post/"+$(this).parent().data('id'),
                       type : 'DELETE'
                   })

                .always(function(response) {
                    if(response.source == 'Group'){
                        location.replace("/Groupe/show/"+response.id)
                    }else{
                        location.replace("/profil/show/"+response.id)
                    }
                });
        });
    }
    deleteComment(){
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

    addComment(){
        $('#createComment').on('submit',function (e) {
            e.preventDefault();
            $.ajax({
                url:"/add/Comment/"+$(this).data('id'),
                data:$( this ).serialize(),
                type:"POST",
                success:function (response) {
                    location.reload();
                }
            })
        })
    }

}

module.exports = User