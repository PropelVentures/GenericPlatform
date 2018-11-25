// When the document is ready
$(document).ready(function () {
var widget = uploadcare.Widget('#file2');
var uploadcare_field = $('#file2');
var old_value = uploadcare_field.val();
var value_changed = false;


function landscapeImages(fileInfo) {
  var imageInfo = fileInfo.originalImageInfo;
  if (imageInfo !== null) {
	if (imageInfo.height / imageInfo.width > 1.0) {
	  throw new Error('portrait');
	}
  }
}

function imagesOnly(fileInfo) {
  if (fileInfo.isImage === false) {
	throw new Error('image');
  }
}

function minDimensions(width, height) {
  return function(fileInfo) {
	var imageInfo = fileInfo.originalImageInfo;
	if (imageInfo !== null) {
	  if (imageInfo.width < width || imageInfo.height < height) {
		throw new Error('dimensions');
	  }
	}
  };
}

widget.validators.push(landscapeImages);
widget.validators.push(imagesOnly);
widget.validators.push(minDimensions(650, 130));

widget.onUploadComplete(function(info) {
	console.log(info);
	//alert(info.cdnUrl);
	$("#project_thumb").attr("src", info.cdnUrl);
	$("#uploadcare_image_url").attr("value", info.cdnUrl);
	$("#uploadcare_image_name").attr("value", info.name);
});

widget.onChange(function(info) {
  //document.getElementById('preview1').src = info.cdnUrl + '/preview/160x120/';
  //alert(info.originalImageInfo.width);
});

function track_value_change()
{
  if(!value_changed && uploadcare_field.val() != old_value)
  {
	value_changed = true;

	var file = uploadcare.fileFrom('uploaded', uploadcare_field.val());
	widget.value(file);
  }
}
setInterval(function() { track_value_change()}, 100);

$(document).on("click", ".uploadcare-widget-buttons-remove", function(){
	value_changed= false;
	$("#project_thumb").attr("src", "project_uploads/defaultImageIcon.png");
	$("#uploadcare_image_url").attr("value", "");
	$("#uploadcare_image_name").attr("value", "");

});



});
function showlistViewspl(pname,tab,view){
    //var tablename=pname;
    //var list=tab;
    //var view=view;
    //var activity = $(this).attr('rel');
    var action = "projects.php";
    var method = "GET";
    $('body').append('<form id="pageNvg"></form>');
    $('#pageNvg').attr("action", action).attr("method", method);
    $('#pageNvg').append(
        '<input type="hidden" name="pname" id="pname" value="'
            + pname + '">').append(
            '<input type="hidden" name="tab" id="tab" value="'
                + tab + '">').append(
            '<input type="hidden" name="listView" id="listView" value="'
                + view + '">');
    $("#pageNvg").submit();
    e.stopPropagation();
}
$(".projectList").on('click',function(){
    var gridType = $(this).attr('id');
    //alert(gridType);
    if(gridType == "list"){
        $("#tablecontent").show(10);
        $("#card-view-container").hide(10);
        $("#xlist-view-container").hide(10);
    }
    if(gridType == "card"){
        $("#card-view-container").show(10);
        $("#xlist-view-container").hide(10);
        $("#tablecontent").hide(10);
    }
    if(gridType == "xlist"){
        $("#xlist-view-container").show(10);
        $("#card-view-container").hide(10);
        $("#tablecontent").hide(10);
    }
    $(".rating-container").html("");
});
$('#expiryDate').datepicker({
	format: "yyyy/mm/dd"
});

$('#create-project').bootstrapValidator({
message: 'This value is not valid',
fields: {
	projectName: {
		validators: {
			notEmpty: {
				message: 'Project Name is required.'
			}
		}
	},
	 projectPrice: {
		validators: {
			notEmpty: {
				message: 'Project Price is required.'
			},
			regexp: {
				regexp: /^[0-9\.]+$/,
				message: 'Project Price can only consist of number, dot.'
			}
		}
	},
	quantity: {
		validators: {
			notEmpty: {
				message: 'Quantity is required and cannot be empty'
			},
			regexp: {
				regexp: /^[0-9]+$/,
				message: 'Quantity can only consist of  numbers.'
			}
		}
	},
	category: {
		validators: {
			notEmpty: {
				message: 'Category is required.'
			}
		}
	}
}
});

function addTagToSuggestion(){
	var tag = $('#addNewTag').val();
	var tags = $('#tags').val();
	if(tag != ""){
		tags = tags.trim();
		if(tags != "" && tags != "undefined"){
			tags = tags+', '+tag;
		}else{
			tags = tag;
		}
		$("#tags").val(tags);
		$("#addNewTag").val("");
		var random_id = Math.floor(Math.random() * 90000) + 10000;
		$('#tags_list').append('<span id="'+random_id+'"> '+tag+' <img alt="" title="Remove" src="http://generic.cjcornell.com/application/images/closebox.png" onclick="removetag(\''+tag+'\', \''+random_id+'\');"></span>');
	}
	else{
		alert("Please Enter Tag Name.");
	}
}

function removetag(tag, tag_id){
	var tags = $('#tags').val();
	var listItem = $( "#"+tag_id );
	var index = $( "#tags_list span" ).index( listItem );

	var array = tags.split(',');
	var index = array.indexOf(array[index]);
	if(index > -1){
		array.splice(index, 1);
	}
	$('#tags').val(array);
	$('#tags_list'+' #'+tag_id).html("").remove();
}