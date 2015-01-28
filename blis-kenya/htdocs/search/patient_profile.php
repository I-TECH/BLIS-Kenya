<?php
#
# Main page for showing patient profile, test history,
# and options like updating profile, registering new specimen
#
include("redirect.php");
include("includes/header.php");
LangUtil::setPageId("patient_profile");

$pid = $_REQUEST['pid'];

?>
<!-- BEGIN PAGE TITLE & BREADCRUMB-->       
                        <h3>
                        </h3>
                        <ul class="breadcrumb">
                            <li>
                                <i class="icon-download-alt"></i>
                                <a href="index.php">Home</a> 
                            </li>
                        </ul>
                        <!-- END PAGE TITLE & BREADCRUMB-->
                    </div>
                </div>
                <!-- END PAGE HEADER-->
             <div class="row-fluid">
                <div class="span12 sortable">

                    <div class="portlet box green" id="patientprofile_div">
                        <div class="portlet-title" >
                            <h4><i class="icon-reorder"></i> <?php echo LangUtil::getTitle(); ?> </h4>           
                        </div>
                        
                          <div class="portlet-body" >
                          
                            <a href='javascript:history.go(-1);'>&laquo; <?php echo LangUtil::$generalTerms['CMD_BACK']; ?></a>
                            <br><br>
                            <table clas="table">
                            	<tr valign='top'>
                            		<td>
                            			<div id='profile_div'>
                            				<?php $page_elems->getPatientInfo($pid); ?>
                            			</div>
                            			<div id='profile_update_div' style='display:none;' >
                            				<?php $page_elems->getPatientUpdateForm($pid); ?>
                            			</div>
                            		</td>
                            		<td>
                            			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            		</td>
                            		<td>
                            			<?php $page_elems->getPatientTaskList($pid); ?>
                            		</td>
                            	</tr>
                            </table>
                            <hr />
                            <br>
                            <b><?php echo LangUtil::$generalTerms['CMD_THISTORY']; ?></b><br>
                            <?php $page_elems->getPatientHistory($pid); ?>
                            <div id="barcodeData" style="display:none;">
                            <input type="text" id="patientID" value='<?php echo encodePatientBarcode($_REQUEST['pid'],0); ?>' />
                            <br><br>
                            <div id="patientBarcodeDiv"></div>
                            <br><br>
                            <div id="specimenBarcodeDiv"></div>
                            </div>
                </div>
          </div>
          </div>
          </div>
                            
<?php
include("includes/scripts.php");
include("barcode/barcode_lib.php");

$script_elems->enableJQueryForm();
$script_elems->enableDatePicker();

$barcodeSettings = get_lab_config_settings_barcode();
//print_r($barcodeSettings);
$code_type = $barcodeSettings['type']; //"code39";
$bar_width = $barcodeSettings['width']; //2;
$bar_height = $barcodeSettings['height']; //40;
$font_size = $barcodeSettings['textsize']; //11;

?>
<script type="text/javascript" src="facebox/facebox.js"></script>
<script type='text/javascript'>
$(document).ready(function(){
    var code = $('#patientID').val();
    $("#patientBarcodeDiv").barcode(code, '<?php echo $code_type; ?>',{barWidth:<?php echo $bar_width; ?>, barHeight:<?php echo $bar_height; ?>, fontSize:<?php echo $font_size; ?>, output:'css'});         
    
});
function toggle_profile_divs()
{
    $('#profile_div').toggle();
    $('#profile_update_div').toggle();
    $('#profile_update_form').resetForm();
}

function print_specimen_barcode(pid, sid)
{
    s_id = parseInt(sid);
    url = "ajax/getSpecimenBarcode.php?sid="+sid;
    $.ajax({
        type: "GET",
        url: url,
                async: false,
        success: function(data) {
                         code = data;

        }
    });
    $("#specimenBarcodeDiv").barcode(code, '<?php echo $code_type; ?>',{barWidth:<?php echo $bar_width; ?>, barHeight:<?php echo $bar_height; ?>, fontSize:<?php echo $font_size; ?>, output:'css'});//initially as output:'bmp'         
    Popup($('#specimenBarcodeDiv').html());
}

function print_patient_barcode()
{
    Popup($('#patientBarcodeDiv').html());
}

function Popup(data) 
    {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        /*mywindow.document.write('<html><head><title>Barcode</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.print();
        mywindow.close();
        //mywindow.document.show
        return true;
    }

function update_profile()
{
    $('#pd_ym').attr("value", "0");
    $('#pd_y').attr("value", "0");
    var yyyy = $('#yyyy').attr("value");
    var mm = $('#mm').attr("value");
    var dd = $('#dd').attr("value");
    var age = $('#age').attr("value");
    var error_message = "";
    var error_flag = 0;
    //Age not given
    if(age.trim() == "")
    {
        //Check partial DoB
        if(yyyy.trim() != "" && mm.trim() != "" && dd.trim() == "")
        {
            dd = "01";
            if(checkDate(yyyy, mm, dd) == false)
            {
                alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$generalTerms['DOB']." ".LangUtil::$generalTerms['INVALID']; ?>");
                return;
            }
            $('#pd_ym').attr("value", "1");
            
        }
        else if(yyyy.trim() != "" && mm.trim() == "" && dd.trim() == "")
        {
            mm = "01";
            dd = "01";
            if(checkDate(yyyy, mm, dd) == false)
            {
                alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$generalTerms['DOB']." ".LangUtil::$generalTerms['INVALID']; ?>");
                return;
            }
            $('#pd_y').attr("value", "1");
        }
        else if(yyyy.trim() == "" && mm.trim() == "" && dd.trim() == "")
        {
            error_message += "Please enter either Age or Date of Birth\n";//<br>";
            error_flag = 1;
            alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$pageTerms['TIPS_AGEORDOB']; ?>");
            return;
        }
        else
        {
            //Full DoB - check
            if(checkDate(yyyy, mm, dd) == false)
            {
                alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$generalTerms['DOB']." ".LangUtil::$generalTerms['INVALID']; ?>");
                return;
            }
        }
    }
    else if (isNaN(age))
    {
        alert("<?php echo LangUtil::$generalTerms['ERROR'].": ".LangUtil::$generalTerms['AGE']." ".LangUtil::$generalTerms['INVALID']; ?>");
        return;
    }   
    
    $('#update_profile_progress').show();
    var params = $('#profile_update_form').formSerialize();
    $.ajax({
        type: "POST",
        url: "ajax/patient_update.php",
        data: params,
        success: function(msg) {
            $('#update_profile_progress').hide();
            window.location.reload();
        }
    }); 
}
</script>
<?php include("includes/footer.php"); ?>
