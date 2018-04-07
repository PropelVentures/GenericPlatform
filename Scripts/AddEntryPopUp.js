$(document).ready(function(){
    $("#AddDialog").dialog({
        autoOpen: false,
        show: {
            effect: "clip",
            duration: 300
        },
        hide: {
            effect: "clip",
            duration: 300
        }
    });
});


function DisplayAddPopUp(){
    $("#AddDialog").dialog("open");
}

function CloseAddPopUp(){
    $("#AddDialog").dialog("close");
}


