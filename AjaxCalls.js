

function ajaxTesting() {
    $.ajax({
        url: "http://home.localhost/GenericNew/GenericPlatform/AjaxPhpPages/UpdateQuery.php",
        type: 'post',
        data: {
            'action': 'test2'
        },
        success: function (data, status) {
            if (data == "ok") {
                console.log("worked");
                $("#test123").text("yay");
            }
            else {
                console.log("???");
            }
        },
        error: function (xhr, desc, err) {
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
        }
    });
}