<div class ="leave_feedback">
    
    <div class ="text">Leave Feedback</div>
    <div class ="close">X</div>
    <div class ="clearfix"></div>
    <div class ="form">
        <form autocomplete="false" id = "feedback_form">
            <input type ="text" name = "lvf_name" maxlength="50" id = "lvf_name" placeholder ="Your name..." required = "required"/>
            <textarea name = "lvf_feedback" id = "lvf_feedback" placeholder ="Your feedback..." required = "required"></textarea>
            <input type ="hidden" name = "lvf_post" id = "lvf_post" value = "<?php echo get_the_ID(); ?>"/>
            <input type ="hidden" name = "lvf_feedbackId" id = "lvf_feedbackId"/>            
            <input type ="submit" value = "Leave Feedback" name = "send_button" id = "send_button"/>
            <input type ="button" value = "cancel" name = "cancel_button" id = "cancel_button"/>
        </form>
    </div>
    
    <div class ="ajax"></div>

</div>