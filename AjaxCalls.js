

function ajaxTesting(databasetablename, oldkey, oldvalue) {

    var dataDictionary = CreateDataDictionary();
    var BASE_URL = "http://home.localhost/GenericNew/GenericPlatform/main.php";
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
            if (data == "success") {
                var redirect = confirm("The value was updated. \nPress OK to go back and cancel to stay on this page.");
                if(redirect == true){
                    var urlParams = getUrlVars();
                    window.location.assign(BASE_URL + "?display=" + urlParams["display"] + "&tab_num=" + urlParams["tab_num"]);
                }
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

function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function login()
{
    var BASE_URL = "http://home.localhost/GenericNew/GenericPlatform/main.php";

    $.ajax({
        url: "http://home.localhost/GenericNew/GenericPlatform/AjaxPhpPages/LoginCheck.php",
        type: 'post',
        data: {
            'username': uname,
            'password': pword
        },
        success: function(data, status){
            if(data == "success"){
                var redirect = confirm("You have been logged in.");
                if(redirect == true){
                    var urlParams = getUrlVars();
                    window.location.assign(BASE_URL + "?display=home" + urlParams["display"] + "&tab_num=" + urlParams["tab_num"]);
                }

            }
            else{

            }
        },
        error: function (xhr, desc, err) {
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
        }
    })
}

function logout()
{
    var BASE_URL = "http://home.localhost/GenericNew/GenericPlatform/main.php";

    $.ajax({
        url: "http://home.localhost/GenericNew/GenericPlatform/AjaxPhpPages/Logout.php",
        //type: 'post',
        data: {

        },
        success: function(data, status){
            if(data == "success"){
                window.location.assign(BASE_URL + "?display=home");
            }
            else{

            }
        },
        error: function (xhr, desc, err) {
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
        }
    })
}