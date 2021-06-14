function deleteGame(id){
    $('#deleteButton').submit(function(event) {
    $.ajax({
        url:'http://localhost:8030/user/wishlist/'+id,
        type: 'DELETE',
        contentType: 'application/json;charset=utf-8',

    }).done(function(data){
        $.ajax({
            url:'/user/wishlist',
            type: 'GET',
            contentType: 'application/json;charset=utf-8',
        })
    }).fail(function(error){
        console.log(error);
    });
});
}
