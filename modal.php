<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>jQuery UI Dialog - Modal form</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
  <style>
    body { font-size: 62.5%; }
    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
  </style>
  <script>
  $(function() {
    var dialog;
 
 
    function copy() {
     
    }
 
    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 150,
      width: 450,
      modal: true,
      buttons: {
        "Copy URL": copy,
         Cancel: function() {
            dialog.dialog( "close" );
        } 
      },
      close: function() {
        
      }
    });
 
    //Opening
    $( "#create-user" ).button().on( "click", function() {
      dialog.dialog( "open" );
    });
  });
  </script>
</head>
<body>
 
<div id="dialog-form" title="Share this design for feedback">
  <form>
    <fieldset>
      <input type="text" name="shareURL" id="shareURL" value="" class="text ui-widget-content ui-corner-all">
    </fieldset>
  </form>
</div>
 

<button id="create-user">Create new user</button>
 
 
</body>
</html>