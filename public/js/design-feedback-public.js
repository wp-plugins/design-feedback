/*
 * The below functions take care of the leaving a feedback modal
 */

function designFeedback_leaveFeedBackClose(){
    
    $( ".designfeedback .leave_feedback" ).hide();
    designFeedback_destroyTempDot();
    designFeedback_leaveFeedBackClearFields();
    
}

function designFeedback_leaveFeedBackOpen( e, offset, viewing ){
    
    // viewing = 0 means Adding
    // viewing = 1 means editing
    
    if (viewing === 0){
        // Make sure fields are empty
        designFeedback_leaveFeedBackClearFields();
    }else{
        designFeedback_destroyTempDot();
    }
    
    // Visibling the modal
    $( ".designfeedback .leave_feedback" ).css( "display", "table" );

    // Retrieving position on the screen
    relX = e.pageX - offset.left;
    relY = e.pageY - offset.top;
    
    // Positioning the modal on the screen
    designFeedback_leaveFeedBackPositioning(relX, relY);
    
    if (viewing === 0){
        // Creating and Position the dot
        designFeedback_createDot(relX, relY);
    }
}

function designFeedback_leaveFeedBackPositioning(X, Y){
    
    X = X + 10;
    Y = Y + 10;

    $( ".designfeedback .leave_feedback" ).css( "left", X + "px" );
    $( ".designfeedback .leave_feedback" ).css( "top", Y + "px" );
    
}

function designFeedback_createDot(X, Y){
    
    // Adjusting position
    X = X - 17;
    Y = Y - 10;
    
    // Make sure it's clean before creating a new one
    designFeedback_destroyTempDot();
    
    // Attaching a new temporary dot
    $( '<div class = "dot" id = "temporary"></div>' ).appendTo( ".designfeedback .mockups_wrapper" );
    $( '#temporary').css( "left", X + "px" );
    $( '#temporary').css( "top", Y + "px" );
    
    designFeedback_refreshDraggables();
    
    // Give it and ID
    dotId = designFeedback_getCurrentDotId();
    $( '#temporary').html( dotId );
    
}

function designFeedback_destroyTempDot(){
    $( '#temporary').remove();
}

function designFeedback_leaveFeedBackClearFields(){
    
    name = $( ".designfeedback .leave_feedback .form #lvf_name").val("");
    feedback = $( ".designfeedback .leave_feedback .form #lvf_feedback").val("");
    feedbackId = $( ".designfeedback .leave_feedback .form #lvf_feedbackId").val("");
    $( ".designfeedback .leave_feedback .form #send_button").removeAttr("disabled");
    
}

function designFeedback_leaveFeedBack(){
    
    // Disabling the button to prevent multiple clicking
    $( ".designfeedback .leave_feedback .form #send_button").attr("disabled", "disabled");
    
    // Reuniting all data
    name = $( ".designfeedback .leave_feedback .form #lvf_name").val();
    feedback = $( ".designfeedback .leave_feedback .form #lvf_feedback").val();
    postId = $( ".designfeedback .leave_feedback .form #lvf_post").val();
    feedbackId = $( ".designfeedback .leave_feedback .form #lvf_feedbackId").val();

    Y = $( "#temporary" ).css("top");
    X = $( "#temporary" ).css("left");
    generatedId = $( "#temporary" ).html();
    
    // Calling Ajax
    designFeedback_leaveFeedBackAjax(name, feedback, X, Y, postId, generatedId, feedbackId);
    
}

function designFeedback_leaveFeedBackAjax(name, feedback, X, Y, postId, generatedId, feedbackId){
    
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: 'action=leave_feedback&name=' + name + '&feedback=' + feedback +
              '&X=' + X + '&Y=' + Y + '&postId=' + postId + '&generatedId=' + generatedId + 
              '&feedbackId=' + feedbackId,
        success: function(){
            // Refreshing feedbacks for the page
            designFeedback_refreshPageFeedBacksAjax(postId);
            // Closing the modal
            $( ".designfeedback .leave_feedback" ).hide();
            designFeedback_leaveFeedBackClearFields();
        }
     });
     
}

function designFeedback_refreshPageFeedBacksAjax(postId){
    
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: 'action=refresh_dots' + '&postId=' + postId,
        beforeSend: function(){
            
        },
        success: function(data){
            // Get rid of them all first
            jQuery(".dot").remove();
            // Put them back 
            $( ".designfeedback .mockups_wrapper" ).append(data);
            designFeedback_refreshDraggables();
        }
    });
}

function designFeedback_getCurrentDotId(){
    
    count = 0;
    
    $( ".dot" ).each(function( index ) {
        count = count+1;
    });
    
    return designFeedback_checkIfDotIdIsTaken(count);
    
}

function designFeedback_checkIfDotIdIsTaken(dotId){
    
    $( ".dot" ).each(function( index ) {
        if ( $(this).html() == dotId){
            dotId = dotId + 1;
            designFeedback_checkIfDotIdIsTaken(dotId);
        }
    });

    return dotId;
    
}

function designFeedback_refreshDraggables(){
    
    // Make it draggable
    $( ".dot" ).draggable({
        drag: function( event, ui ) {
            X = ui.position.left;
            Y = ui.position.top;
            //Summing back what's been subtracted before so that the modal goes to the right position
            X = X + 20;
            Y = Y + 10;
            designFeedback_leaveFeedBackPositioning(X, Y);
        },
        containment: ".mockups_wrapper",
        stop: function( event, ui ){
            
            dotId = $(this).attr("id");
            if (dotId != "temporary"){
                X = ui.position.left;
                Y = ui.position.top;
                designFeedback_updateDotPosition(dotId, X, Y);
            }
            
        }
    });
    
  
    
    $( ".dot" ).on( "click", function(e){
        e.preventDefault();
        e.stopPropagation();
        parentOffset = $(this).parent().offset();
        //Making sure the dot stays visible
        $( ".dot" ).css("z-index", 100);
        $( this ).css("z-index", 501);
        designFeedback_dotInAjax( $(this).attr("id"), e, parentOffset );
    });
    
    
    
}

function designFeedback_updateDotPosition(dotId, X, Y){
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: 'action=update_dot' + '&dotId=' + dotId + '&X=' + X + '&Y=' + Y,
        beforeSend: function(){
            
        },
        success: function(){
            
        }
    });
}


function designFeedback_dotInAjax( dotId, e, parentOffset ){
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: 'action=get_dot_data' + '&dotId=' + dotId,
        beforeSend: function(){
            designFeedback_leaveFeedBackOpen(e, parentOffset, 1);
            $(".leave_feedback #lvf_feedbackId").val(dotId);
        },
        success: function(data){
            $(".leave_feedback .ajax").html(data);
        }
    });
}
/*
function designFeedback_dotIn(dot){
    
    if ( $(dot).attr("id") != "temporary" ){
        $(".view_feedback").css("opacity", ".9");
        $(".view_feedback").css("left", $( dot ).css("left"));
        $(".view_feedback").css("top", $( dot ).css("top"));
        $(".view_feedback").css("margin-left", "50px");
        $(".view_feedback").css("z-index", "500");

        designFeedback_dotInAjax( $( dot ).attr("id") );
    }
    
}

function designFeedback_dotOut(dot){
    $(".view_feedback").css("opacity", "0");
    $(".view_feedback").css("z-index", "99");
}

function designFeedback_dotInAjax( dotId ){
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: 'action=get_dot_data' + '&dotId=' + dotId,
        beforeSend: function(){
            
        },
        success: function(data){
            $(".view_feedback .ajax").html(data);
        }
    });
}
*/