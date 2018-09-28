$(document).ready(function(){
	var widget1 = uploadcare.Widget('#file1');
	var uploadcare_field = $('#file1');
	var old_value = uploadcare_field.val();
	var value_changed = false;
	widget1.validators.push(imagesOnly);
	widget1.validators.push(minDimensions(650, 130));
	
	UPLOADCARE_LOCALE_TRANSLATIONS = {
	  errors: {
		'portrait': 'Landscape images only',  // message for widget
		'dimensions': 'Dimensions should be more or equal to 650 X 130'
	  },
	  dialog: {tabs: {preview: {error: {
		'portrait': {  // messages for dialog's error page
		  title: 'No portrait images are allowed.',
		  text: 'We are sorry but image should be landscape.',
		  back: 'Back'
		}
	  } } } }
	};
	
	function landscapeImages(fileInfo) {
	  var imageInfo = fileInfo.originalImageInfo;
	  if (imageInfo !== null) {
		if (imageInfo.height / imageInfo.width > 1.0) {
		  throw new Error('portrait');
		}
	  }
	}
	
	widget1.validators.push(landscapeImages);
	
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
	widget1.onUploadComplete(function(info) {
		console.log(info);
		$("#uploadcare_image_url").attr("value", info.cdnUrl);
		$("#uploadcare_image_name").attr("value", info.name);
	});

	widget1.onChange(function(info) {
	  //document.getElementById('preview1').src = info.cdnUrl + '/preview/160x120/';
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
		$("#uploadcare_image_url").attr("value", info.cdnUrl);
		$("#uploadcare_image_name").attr("value", info.name);

	});



});