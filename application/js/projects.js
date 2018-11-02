jQuery(document).ready(function(){

	jQuery(".myProjectSearch").keyup(function(){
		var searchbox = jQuery(this).val();
		var dataString = searchbox;
		if(searchbox==''){
			jQuery.ajax({
				type: "GET",
				url: "ajax-actions.php?action=search_projects&project="+searchbox,
				success: function(data)
				{
					jQuery("#pdwrapper").html(data);
				}
			});
		}
		else{
			jQuery.ajax({
			type: "GET",
			url: "ajax-actions.php?action=search_projects&project="+searchbox,
			success: function(data){
				jQuery("#pdwrapper").html(data);
			}
			});
		}
	return false;    
	});
		
	jQuery("#myprojects").click(function(){	
		jQuery.ajax({
				type: "GET",
				url: "ajax-actions.php?action=myprojects",
				success: function(data)
				{
					jQuery("#pdwrapper").html(data);
				}
			});	
	});
	
	jQuery("#projects").click(function(){
	
			jQuery("#searchProjects div:last-child").html("<input type='text' id='searchOthersProjects' class='input-bg form-control myProjectSearch' onkeyup='searchProjects()'/>");
			jQuery.ajax({
				type: "GET",
				url: "ajax-actions.php?action=projects",
				success: function(data)
				{
					jQuery("#pdowrapper").html(data);
				}
			});	
	});
	
	
});

function searchProjects(){
	// alert("test");
		var searchbox=document.getElementById("searchOthersProjects").value;
		var dataString = searchbox;
		if(searchbox==''){
			jQuery.ajax({
				type: "GET",
				url: "ajax-actions.php?action=searchOthersProjects&project="+searchbox,
				success: function(data)
				{
					jQuery("#pdowrapper").html(data);
				}
			});
		}
		else{
			jQuery.ajax({
			type: "GET",
			url: "ajax-actions.php?action=searchOthersProjects&project="+searchbox,
			success: function(data){
				jQuery("#pdowrapper").html(data);
			}
			});
		}
	return false;    
	}
