<?php
$meta = get_post_meta(get_the_ID(),"image_design");
$image = wp_get_attachment_image( $meta[0], "full" );
?>
<html>
    <head>
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url( __FILE__ ) . '../css/design-feedback-public.css'; ?>"/>
        <title><?php echo get_the_title(); ?></title>
        <script>
            // Ajax Support
            var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' );?>';
        </script>
    </head>
    <body class = "designfeedback">
        
        <?php require_once("partials/feedback-box.php"); ?>
        
        <div class ="loading">
            <div class = "text">A design is loading...</div>
            <div class = "bar"></div>
            <script>
                var loadingBar = $( ".bar" ); 
                
                function changeLoadingStatus(prcnt){
                    loadingBar.progressbar({ value: prcnt });
                    
                    if ( prcnt === 100){
                        loadingBar.progressbar({ value: false });
                        loaded();
                    }
                }
                changeLoadingStatus();
            </script>
            <div class = "copyright">Powered by <b>Cycles</b>, the best way to share designs and collect feedback with WordPress</div>
        </div>
        
        <div class ="loaded">
            <div class = "text"><?php echo get_the_title();?> is ready.</div>
            <div class = "subtext">Click anywhere on the design to leave feedback.</div>
            <div class = "continue">Continue</div>
        </div>
        
        <script>setTimeout(changeLoadingStatus, 100, 15);</script>
        
        <div class ="mockups_wrapper">
            <script>setTimeout(changeLoadingStatus, 100, 20);</script>
            <?php echo $image; ?>
            <script>setTimeout(changeLoadingStatus, 100, 80);</script>
        </div>
        
        <script src="<?php echo plugin_dir_url( __FILE__ ) ?>../js/design-feedback-public.js"></script>
        <script>
        /*
         * On Click functions
         */
        $( ".designfeedback .loaded .continue" ).click(function(){
            $(".loaded").hide();
            $(".mockups_wrapper").show();
            $("body").css("backgroundColor","#fff");
        });
        
        $( ".designfeedback .leave_feedback .close" ).click(function(){
            designFeedback_leaveFeedBackClose();
        });
        
        $( ".designfeedback .leave_feedback #cancel_button" ).click(function(){
            designFeedback_leaveFeedBackClose();
        });
        
        $( ".designfeedback .mockups_wrapper" ).click(function(e){
            parentOffset = $(this).parent().offset();
            $( ".dot" ).css("z-index", 100);
            designFeedback_leaveFeedBackOpen(e, parentOffset, 0);
        });
       
        $( ".designfeedback .leave_feedback .form #feedback_form" ).on( "submit", function( event ) {
            event.preventDefault();
            designFeedback_leaveFeedBack();
        });
        
        /*
         * Ready
         */
        
        $( document ).ready( function() {
            designFeedback_refreshPageFeedBacksAjax('<?php echo get_the_ID(); ?>');
            setTimeout(changeLoadingStatus, 100, 100);
        });
        
        
        /*
         * Below functions had to be left in here, they take care of the loading bar
         */
        function loaded(){
            setTimeout(loaded2, 1000);
        }
        
        function loaded2(){
            $(".loading").hide();
            $(".loaded").show();
        }
        </script>
    </body>
</html>