class Search {
    constructor() {
        this.search();
    }
    search(){
        $('.btn_Close').click(function () {
            $('#search_Bar_Result').addClass('none');
        })

        function log( ui ) {

            for (var i = 0; i<ui.content.length;i++){
                var info = $('<li><a class="section_Search_User" href="/profil/show/'+ui.content[i].value+'">'+ui.content[i].label+'</a></li>' );
                info.prependTo( "#search_Bar_Result_Info" );
            }
        }
        $('#search').autocomplete({
            source: "/user/search",
            minLength: 2,
            response: function( event, ui ) {
                console.log(ui)
                if(ui.content.length > 0){
                $('#search_Bar_Result').removeClass('none');
                $('#search_Bar_Result_Info').empty();
                log(ui);
                }
            }
        });
    }
}