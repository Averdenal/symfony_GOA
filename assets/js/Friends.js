module.exports = function() {
    $( "#add_Friend" ).on( 'click', function () {
        $(this).data( "id" );
        $.ajax( {
                    method: "GET",
                    url: "/friend/add/"+$(this).data( "id" ),
                    success: function (response) {
                        location.reload();
                    }
                } )
    } );
    $( "#delete_Friend" ).on( 'click', function () {
        $(this).data( "id" );
        $.ajax( {
                    method: "DELETE",
                    url: "/friend/delete/"+$(this).data( "id" ),
                    success: function (response) {
                        location.reload();
                    }
                })
    });
};
