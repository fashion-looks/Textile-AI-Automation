<style>
@media (min-width: 992px){
  .modal-lg, .modal-xl {
      max-width: 80%;
  }
}
</style>
<script type="text/javascript">
function upperCaseF(a){
    setTimeout(function(){
        a.value = a.value.toUpperCase();
    }, 1);
}
$(document).ready(function() {
 $(".inputborder").attr("autocomplete", "nope");
});
</script>
  <script>
 /* $(document).ready(function(){
    $("#myInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#myTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
      
    });
  });*/
 
  </script>
  <div style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; background-color: #000000c7; z-index: 9999; display:none;" id="processBar">
	<div style="padding:20px; background-color:#FFFFFF; margin:auto; width:300px; margin-top:10%; text-align:center; border-radius: 10px;color: green;"><img src="img/Spin2.gif" width="100px;"><br>Loading... Please wait</div>
</div>

  <script>
function processBar(){
	$('#processBar').show();
}
</script>
<style type="text/css">
input[type=text]:focus {
  border: 2px solid #5a48e9;
}
input:focus {
  border: 2px solid #5a48e9;
}
label:focus {
  border: 2px solid #5a48e9;
}
select:focus {
  border: 2px solid #5a48e9;
}
number:focus {
  border: 2px solid #5a48e9;
}
.form-control:focus, .dd-handle:focus {
     border-color: #5a48e9;
}
</style>
<div class="container"> 
  <div id="modalpop" class="modal fade" style="margin:auto;">
	<div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #63af32;">
          <h6 class="modal-title" id="titleheader" style="color: white">Info header</h6>
        </div>
        <div id="loadurl">Loading..</div>
      </div>
    </div>
  </div> 
</div>
<script>
function opmodalpop(header,url,width,height){
  $('#titleheader').text(header);
  $('#modalpop').css('width',width);
  $('#modalpop').css('height',height);
  $('#loadurl').load(encodeURI(url));
}
</script>
<script type="text/javascript">
//Put our input DOM element into a jQuery Object
var $jqDate1 = jQuery('input[name="acknwoledmentdate"]');
//Bind keyup/keydown to the input
$jqDate1.bind('keyup','keydown', function(e){
  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
  if(e.which !== 8) { 

    let numChars = $jqDate1.val().length;
    if(numChars === 2 || numChars === 5){
      let thisVal = $jqDate1.val();
      thisVal += '-';
      $jqDate1.val(thisVal);
    }
  }
});
var $jqDate2 = jQuery('input[name="applicationdate"]');
//Bind keyup/keydown to the input
$jqDate2.bind('keyup','keydown', function(e){
  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
  if(e.which !== 8) { 

    let numChars = $jqDate2.val().length;
    if(numChars === 2 || numChars === 5){
      let thisVal = $jqDate2.val();
      thisVal += '-';
      $jqDate2.val(thisVal);
    }
  }
});
var $jqDate3 = jQuery('input[name="DateAgreement"]');
//Bind keyup/keydown to the input
$jqDate3.bind('keyup','keydown', function(e){
  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
  if(e.which !== 8) { 

    let numChars = $jqDate3.val().length;
    if(numChars === 2 || numChars === 5){
      let thisVal = $jqDate3.val();
      thisVal += '-';
      $jqDate3.val(thisVal);
    }
  }
});

var $jqDate4 = jQuery('input[name="DOB"]');
//Bind keyup/keydown to the input
$jqDate4.bind('keyup','keydown', function(e){
  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
  if(e.which !== 8) { 

    let numChars = $jqDate4.val().length;
    if(numChars === 2 || numChars === 5){
      let thisVal = $jqDate4.val();
      thisVal += '-';
      $jqDate4.val(thisVal);
    }
  }
});

//Put our input DOM element into a jQuery Object
var $jqDate5 = jQuery('input[name="verificationdate"]');
//Bind keyup/keydown to the input
$jqDate5.bind('keyup','keydown', function(e){
  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
  if(e.which !== 8) { 

    var numChars = $jqDate5.val().length;
    if(numChars === 2 || numChars === 5){
      var thisVal = $jqDate5.val();
      thisVal += '-';
      $jqDate5.val(thisVal);
    }
  }
});

var $jqDate6 = jQuery('input[name="ActivationDate"]');
//Bind keyup/keydown to the input
$jqDate6.bind('keyup','keydown', function(e){
  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
  if(e.which !== 8) { 

    var numChars = $jqDate6.val().length;
    if(numChars === 2 || numChars === 5){
      var thisVal = $jqDate6.val();
      thisVal += '-';
      $jqDate6.val(thisVal);
    }
  }
});

var $jqDate7 = jQuery('input[name="ClosureDate"]');
//Bind keyup/keydown to the input
$jqDate7.bind('keyup','keydown', function(e){
  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
  if(e.which !== 8) { 

    var numChars = $jqDate7.val().length;
    if(numChars === 2 || numChars === 5){
      var thisVal = $jqDate7.val();
      thisVal += '-';
      $jqDate7.val(thisVal);
    }
  }
});

var $jqDate8 = jQuery('input[name="TrainingDate"]');
//Bind keyup/keydown to the input
$jqDate8.bind('keyup','keydown', function(e){
  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
  if(e.which !== 8) { 

    var numChars = $jqDate8.val().length;
    if(numChars === 2 || numChars === 5){
      var thisVal = $jqDate8.val();
      thisVal += '-';
      $jqDate8.val(thisVal);
    }
  }
});

var $jqDate9 = jQuery('input[name="dateofdescreresolution"]');
//Bind keyup/keydown to the input
$jqDate9.bind('keyup','keydown', function(e){
  //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
  if(e.which !== 8) { 

    var numChars = $jqDate9.val().length;
    if(numChars === 2 || numChars === 5){
      var thisVal = $jqDate9.val();
      thisVal += '-';
      $jqDate9.val(thisVal);
    }
  }
});

</script>
<iframe id="actoinfrm" name="actoinfrm" src="" style="display:none;"></iframe>
<script src="js/jquery.Jcrop.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="vendors/popper.js/dist/umd/popper.min.js"></script>
<script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Slimscroll JavaScript -->
<script src="js/jquery.slimscroll.js"></script>

<!-- FeatherIcons JavaScript -->
<script src="js/feather.min.js"></script>
<!-- Owl JavaScript -->
<script src="vendors/owl.carousel/dist/owl.carousel.min.js"></script>

<!-- Toastr JS -->
<script src="vendors/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

<script src="js/init.js"></script>

<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<!--<script src="js/jquery.validate.min.js"></script>-->
<link rel="stylesheet" href="css/chosen.min.css">
<script src="js/chosen.jquery.min.js"></script>
<script src="js/jquery.validate.min.js"></script>