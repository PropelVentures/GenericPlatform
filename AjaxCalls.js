

function ajaxTesting(databasetablename, oldkey, oldvalue) {

    var dataDictionary = CreateDataDictionary();

    $.ajax({
        url: "http://home.localhost/GenericNew/GenericPlatform/AjaxPhpPages/UpdateQuery.php",
        type: 'post',
        data: {
            'UpdatedValues': dataDictionary,
            'OldKey': oldkey,
            'OldValue': oldvalue,
            'DatabaseTableName': databasetablename
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

function CreateDataDictionary(){
    var dict = {};
    $("form :input[type=text]").each(function(){
            dict[$(this).attr("name")] = $(this).val();
        });
    return dict;
}