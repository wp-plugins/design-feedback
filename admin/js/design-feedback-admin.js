function designFeedback_loadImage(){
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: 'action=get_image&image='+ jQuery('#meta-image').val(),
        beforeSend: function(){
            // Handle the beforeSend event
        },
        success: function(data){
           jQuery(".meta_box_no_image").hide();
           jQuery(".meta_box_image").show();
           jQuery(".meta_box_image").html(data);
        }
     });
}

function designFeedback_removeImage(){
    jQuery('#meta-image').val("");
    jQuery(".meta_box_no_image").show();
    jQuery(".meta_box_image").hide();
}

function designFeedback_openShareWindow(permalink){
    jQuery("#designFeedback_result").hide();
    jQuery("#copy-button").data( "clipboard-text", permalink );
    
 
    modal.dialog( "open" );
    jQuery("#shareURL").val(permalink);
}

function designFeedback_deleteFeedback(feedbackId){
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: 'action=delete_feedback&feedbackId='+ feedbackId,
        beforeSend: function(){
            // Handle the beforeSend event
        },
        success: function(){
           location.reload();
        }
    });
}