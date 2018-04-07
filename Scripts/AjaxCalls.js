

function UpdateValue(databasetablename, oldkey, oldvalue) {

    var dataDictionary = CreateDataDictionary(false);
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

function AddValues(databasetablename){
    var dataDictionary = CreateDataDictionary(true);
    var BASE_URL = "http://home.localhost/GenericNew/GenericPlatform/main.php";

    $.ajax({
        url: "http://home.localhost/GenericNew/GenericPlatform/AjaxPhpPages/AddQuery.php",
        type: 'post',
        data: {
            'AddedValues': dataDictionary,
            'DatabaseTableName': databasetablename
        },
        success: function (data, status) {
            $("#AddSuccess").val(data);
            $("#AddSuccess").show();
            //Try auto-updating what was just added. Value could potentially change if invalid data is entered.
            // $("#example > tbody:last-child").append(function(){
            //     var totalString = "<tr><td><input type='checkbox'></td>";
            //     dataDictionary.forEach(function(element){
            //         totalString += '<td>' + element + "</td>";
            //     });
            //     totalString += "</tr>"
            //     return totalString;
            // });
        },
        error: function (xhr, desc, err) {
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
        }
    });
}

function DeleteValues(databasetablename, primarykey, primaryfieldname){
    //Get col associated with primary key
    var result = confirm("Are you sure you would like to \ndelete the selected fields?");
    if(result){
        console.log(selectedIndex);
        var colIndex = $("th:contains('" + primaryfieldname +"')").index() + 1;
        valsToDelete = [];
        for(var i = 0; i < selectedIndex.length; i++){
            var valToDelete = $("tr:nth-child(" + selectedIndex[i] + ")");
            var columnThing = valToDelete.find('td:nth-child(' + colIndex + ')');
            while(columnThing.children().length > 0){
                columnThing = columnThing.children().first();
            }
            if(columnThing.val() == ""){
                valsToDelete.push(columnThing.text());
            }
            else{
                valsToDelete.push(columnThing.val());
            }
        }

        $.ajax({
            url: "http://home.localhost/GenericNew/GenericPlatform/AjaxPhpPages/DeleteQuery.php",
            type: 'post',
            data: {
                'ValuesToDelete': valsToDelete,
                'PrimaryKey': primarykey,
                'DatabaseTableName': databasetablename

            },
            success: function (data, status) {
                if(data == "success"){
                    alert("Deleted!");
                    location.reload();
                }
                //Try auto-updating what was just added. Value could potentially change if invalid data is entered.
                // $("#example > tbody:last-child").append(function(){
                //     var totalString = "<tr><td><input type='checkbox'></td>";
                //     dataDictionary.forEach(function(element){
                //         totalString += '<td>' + element + "</td>";
                //     });
                //     totalString += "</tr>"
                //     return totalString;
                // });
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }
}

function CreateDataDictionary(FromAddDialog){
    var dict = {};
    if(FromAddDialog == true){
        $("#AddDialogContent").children().each(function(index, element){
            var currentKey = $(this).find("label").text().trim();
            var currentValue = $(this).find(":input[type=text]").val().trim();
            dict[currentKey] = currentValue;
        });
    }
    else{
        $("form :input[type=text]").each(function(){
            dict[$(this).attr("name")] = $(this).val();
        });
    }

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