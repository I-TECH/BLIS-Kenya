<?php
#
# Results entry page
# Technicians can search for a specimen to enter results for OR import results from a file and validate
#
include("redirect.php");
include("includes/header.php");
LangUtil::setPageId("results_entry");
$lab_config = LabConfig::getById($_SESSION['lab_config_id']);
?>
<!-- BEGIN PAGE TITLE & BREADCRUMB-->		
						<h3>
						</h3>
						<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								<a href="index.html">Home</a> 
								<span class="icon-angle-right"></span>
							</li>
							<li><a href="#">Tests</a>
							<span class="icon-angle-right"></span></li>
							<li><a href="#"></a></li>
						</ul>
						<!-- END PAGE TITLE & BREADCRUMB-->
					</div>
				</div>
				<!-- END PAGE HEADER-->
<div class='batch_results_subdiv_help' id='batch_results_subdiv_help' style='display:none;'>
	<?php
		//$tips_string = LangUtil::$pageTerms['TIPS_INFECTIONSUMMARY'];
		$tips_string = "If you cannot see any information other than Test Name, Results and the Skip Option, please tell your administrator to configure it from Worksheet Configuration";
		$page_elems->getSideTip(LangUtil::$generalTerms['TIPS'], $tips_string);
	?>
</div>

<!-- BEGIN ROW-FLUID-->   
<div class="row-fluid">
<div class="span12 sortable">

<!-- BEGIN PENDING TESTS PORTLET-->	
<div id="pending_tests" class='results_subdiv' style='display:none;'>
	<div class="portlet box blue">
		<div class="portlet-title">
			<h4><i class="icon-reorder"></i><?php echo "Pending Tests";?></h4>
			<div class="tools">
				<a href="javascript:;" class="collapse"></a>
				<a href="#portlet-config" data-toggle="modal" class="config"></a>
				<a href="javascript:fetch_pending_results();" class="reload"></a>
				<a href="javascript:;" class="remove"></a>
			</div>
		</div>
		<div class="portlet-body">
			<div class="scroller" data-height="400px" data-always-visible="1">
				<div id='fetched_specimens_entry'>
				<!--PENDING SPECIMENTS LOADED IN THIS DIV-->
				</div>
				<div id="fetched_specimen">
				<?php
					if(isset($_REQUEST['ajax_response']))
						echo $_REQUEST['ajax_response'];
				?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END PENDING TESTS PORTLET-->

<!-- BEGIN PENDING RESULTS PORTLET-->		
<div id="pending_results" class='results_subdiv' style='display:none;'>
	<div class="portlet box blue">
		<div class="portlet-title">
			<h4><i class="icon-reorder"></i><?php echo "Pending Results";?></h4>
			<div class="tools">
				<a href="javascript:;" class="collapse"></a>
				<a href="#portlet-config" data-toggle="modal" class="config"></a>
				<a href="javascript:fetch_pending_results();" class="reload"></a>
				<a href="javascript:;" class="remove"></a>
			</div>
		</div>
		<div class="portlet-body">
			<div class="scroller" data-height="400px" data-always-visible="1">
				<div id='fetched_specimens_entry'>
				<!--PENDING RESULTS LOADED IN THIS DIV-->
				</div>
				<div id='fetched_pending_results_entry'>
				<!--PENDING RESULTS FETCHED IN THIS DIV-->
				</div>
				<div id="fetched_specimen">
				<?php
				if(isset($_REQUEST['ajax_response']))
					echo $_REQUEST['ajax_response'];
				?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END PENDING RESULTS PORTLET-->	




		<div id="worksheet_results" class='results_subdiv' style='display:none;'>
			<form name="fetch_worksheet" id="fetch_worksheet">
				<b>Worksheet Results</b>
				<br>
				<br>
				Worksheet# <input type="text" name="worksheet_num" id="worksheet_num" class='uniform_width' />
				<input type="button" onclick="fetch_worksheets();" value="Fetch"/>
			</form>
			<div id="worksheet">
			</div>
		</div>
		
		<div id="specimen_results" class='results_subdiv' style='display:none;'>
			<form name="fetch_specimen_form" id="fetch_specimen_form">
				<div class="panel-heading"><span class='page_title'><?php echo LangUtil::$pageTerms['MENU_SINGLESPECIMEN']; ?></span></div>	 
				<select name='resultfetch_attrib' id='resultfetch_attrib'>
					<?php
					$hide_patient_name = true;
					//if($lab_config->hidePatientName == 1)
					if($_SESSION['user_level'] == $LIS_TECH_SHOWPNAME)
					{
						$hide_patient_name = false;
					}
					$page_elems->getPatientSearchAttribSelect($hide_patient_name);
					if($_SESSION['s_addl'] != 0)
					{
					?>
						<option value='5'><?php echo LangUtil::$generalTerms['SPECIMEN_ID']; ?></option>
					<?php
					}
					?>
				</select>
				&nbsp;&nbsp;
				<input type="text" name="specimen_id" id="specimen_id" class='uniform_width' />
				<input type="button" id='fetch_specimen_button' onclick="fetch_specimen();" value="<?php echo LangUtil::$generalTerms['CMD_SEARCH']; ?>" />
				&nbsp;&nbsp;
				<span id='fetch_progress_bar' style='display:none;'>
					<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_SEARCHING']); ?>
				</span>	
			</form>
			<br>
			<div id='fetched_patient_entry'>
			</div>
			<div id="fetched_specimen">
			<?php
				if(isset($_REQUEST['ajax_response']))
					echo $_REQUEST['ajax_response'];
			?>
			</div>
		</div>

		<div id="import_results" class='results_subdiv' style='display:none;'>
			<b>Import Results</b>
			<br>
			<br>
			<form name='form_import' id='form_import' action='' method='POST' enctype='multipart/form-data'>
				<table>
					<tr>
						<td>Machine Type</td>
						<td><input type='text' name='mc_type'></td>
					</tr>
					<tr>
						<td>File</td>
						<td><input type='file' name='file_path'></td>
					</tr>
					<tr>
						<td></td>
						<td><br><input type='button' name='submit_import' value='Import Results'/></td>
					</tr>
				</table>
			</form>
		</div>
		
		<div id='batch_results' class='results_subdiv' style='display:none;'>
			<div class="panel-heading"><span class='page_title'><?php echo LangUtil::$pageTerms['MENU_BATCHRESULTS']; ?></span></div>	 
			<?php echo LangUtil::$generalTerms['TEST_TYPE']; ?>
			&nbsp;&nbsp;&nbsp;
			<select id='batch_test_type' class='uniform_width'>
				<option value=""><?php echo LangUtil::$generalTerms['SELECT_ONE']; ?>..</option>
				<?php $page_elems->getTestTypesSelect($_SESSION['lab_config_id']); ?>
			</select>
			&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;
			<br><br>
			<table>
				<tr valign='top'>
					<td><?php echo LangUtil::$generalTerms['FROM_DATE']; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td>
					<?php
					$today = date("Y-m-d");
					$today_array = explode("-", $today);
					$monthago_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($today)) . " -270 days"));
					$monthago_array = explode("-", $monthago_date);
					$name_list = array("yyyy_from", "mm_from", "dd_from");
					$id_list = array("yyyy_from", "mm_from", "dd_from");
					$value_list = $monthago_array;
					//$page_elems->getDatePicker($name_list, $id_list, $value_list);
					?>
					</td>
				</tr>
				<tr valign='top'>
					<td><?php echo LangUtil::$generalTerms['TO_DATE']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
					<td>
					<?php
						$name_list = array("yyyy_to", "mm_to", "dd_to");
						$id_list = array("yyyy_to", "mm_to", "dd_to");
						$value_list = $today_array;
						//$page_elems->getDatePicker($name_list, $id_list, $value_list);
					?>
					</td>
				</tr>
				<tr valign='top'>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td>
						&nbsp;&nbsp;&nbsp;
						<input type='button' onclick='javascript:get_batch_form();' value='<?php echo LangUtil::$generalTerms['CMD_SEARCH']; ?>'></input>
					</td>
				</tr>
			</table>
			<span id='batch_progress_form' style='display:none'>
				<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_FETCHING']); ?>
			</span>
			<span id='batch_result_error' class='error_string' style='display:none;'>
				<?php echo LangUtil::$generalTerms['MSG_SELECT_TTYPE']; ?>
			</span>
			<br><br>
			<div id='batch_form_div'>
			</div>
		</div>
		
		<div id='verify_results' class='results_subdiv' style='display:none;'>
			<div class="panel-heading"><span class='page_title'><?php echo LangUtil::$pageTerms['MENU_VERIFYRESULTS']; ?></span></div>
			<form name='verify_results_form' id='verify_results_form' action='results_verify.php' method='post'>
				<?php echo LangUtil::$generalTerms['TEST_TYPE']; ?>
				&nbsp;&nbsp;&nbsp;
				<select id='verify_test_type' name='t_type' class='uniform_width'>
					<option value=""><?php echo LangUtil::$generalTerms['SELECT_ONE']; ?>..</option>
					<?php $page_elems->getTestTypesSelect($_SESSION['lab_config_id']); ?>
				</select>
				&nbsp;&nbsp;&nbsp;
				<input type='button' onclick='javascript:get_verification_form();' value='<?php echo LangUtil::$generalTerms['CMD_SEARCH']; ?>'></input>
				&nbsp;&nbsp;&nbsp;
				<span id='verify_progress_form' style='display:none'>
					<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_FETCHING']); ?>
				</span>
				<span id='verify_result_error' class='error_string' style='display:none;'>
					<?php echo LangUtil::$generalTerms['MSG_SELECT_TTYPE']; ?>
				</span>
			</form>
			<br><br>
			<div id='verify_form_div'>
			</div>
		</div>
		
		<div id='control_testing' class='results_subdiv' style='display:none;'>
			<div class="panel-heading"><span class='page_title'><?php echo LangUtil::$pageTerms['CONTROL_TESTING_RESULTS']; ?></span></div>
			<form name='control_testing_form' id='control_testing_form' action='control_testing_entry.php' method='post'>
				<table cellspacing='4px'>
					<tbody>
					<tr valign='top'>
						<td><?php echo LangUtil::$generalTerms['TEST_TYPE']; ?> &nbsp;&nbsp;&nbsp;</td>
						<td>
							<select id='verify_test_type_control' name='t_type' class='uniform_width'>
								<option value=""><?php echo LangUtil::$generalTerms['SELECT_ONE']; ?>..</option>
								<?php $page_elems->getTestTypesSelect($_SESSION['lab_config_id']); ?>
							</select>
							<span id='control_testing_error' class='error_string' style='display:none;'>
								<?php echo LangUtil::$generalTerms['MSG_SELECT_TTYPE']; ?>
							</span>
							<br>
						</td>
					</tr>
					<tr valign='top'>
						<td>Result</td>
						<td>
							<input type="radio" name="controlTesting" id="controlTesting" value="Pass" checked> Pass 
							<input type="radio" name="controlTesting" id="controlTesting" value="Fail"> Fail
							<br>
						</td>
					<tr valign='top'>
						<td></td>
						<td>
							<input type='button' onclick='javascript:verify_control_selection();' value='<?php echo LangUtil::$generalTerms['CMD_SUBMIT']; ?>'></input>
						</td>
					</tr>
					</tbody>
				</table>
			</form>
			<br><br>
			<div id='control_testing_div'>
			</div>
			<div class='clean-orange' id='control_result_done' style='width:300px' style='display:none;'>
						
			</div>
		</div>
		
		<div id='worksheet_div' class='results_subdiv' style='display:none;'>
			<div class="panel-heading"><span class='page_title'><?php echo LangUtil::$pageTerms['MENU_WORKSHEET']; ?></span></div>
			<form name='worksheet_form' id='worksheet_form' action='worksheet.php' method='post' target='_blank'>
				<table cellspacing='4px'>
					<tbody>
					<tr valign='top'>
						<td><?php echo LangUtil::$generalTerms['LAB_SECTION']; ?></td>
						<td>
							<select name='cat_code' id='cat_code' class='uniform_width'>
								<?php $page_elems->getTestCategorySelect(); ?>
							</select>
						</td>
					</tr>
					<tr valign='top'>
						<td><?php echo LangUtil::$generalTerms['TEST_TYPE']; ?><br>OR</td>
						<td>
							<select id='worksheet_test_type' name='t_type' class='uniform_width'>
								<?php $page_elems->getTestTypesSelect($_SESSION['lab_config_id']); ?>
							</select>
						</td>
					</tr>
					<tr valign='top'>
						<td>
							<?php echo LangUtil::$pageTerms['CUSTOM_WORKSHEET']; ?></td>
						<td>
							<select id='worksheet_custom_type' name='w_type' class='uniform_width'>
								<option value=""><?php echo LangUtil::$generalTerms['SELECT_ONE']; ?></option>
								<?php 
								$lab_config = LabConfig::getById($_SESSION['lab_config_id']);
								$page_elems->getCustomWorksheetSelect($lab_config); 
								?>
							</select>
						</td>
					</tr>
					<tr valign='top'>
						<td><?php echo LangUtil::$pageTerms['BLANK_WORKSHEET']; ?>?</td>
						<td>
							<input type='radio' name='is_blank' value='Y'><?php echo LangUtil::$generalTerms['YES']; ?></input>
							<input type='radio' name='is_blank' value='N' checked><?php echo LangUtil::$generalTerms['NO']; ?></input>
						</td>
					</tr>
					<tr valign='top' id='num_rows_row' style='display:none;'>
						<td><?php echo LangUtil::$pageTerms['NUM_ROWS']; ?></td>
						<td>
							<input type='text' name='num_rows' id='num_rows' value='10' class='uniform_width'></input>
						</td>
					</tr>
					<tr valign='top'>
						<td></td>
						<td>
							<input type='button' onclick='javascript:get_worksheet();' value='<?php echo LangUtil::$generalTerms['CMD_SUBMIT']; ?>'></input>
							&nbsp;&nbsp;&nbsp;
							<span id='worksheet_progress_form' style='display:none'>
								<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_FETCHING']); ?>
							</span>
							<span id='worksheet_error' class='error_string' style='display:none;'>
								<?php echo LangUtil::$generalTerms['MSG_SELECT_TTYPE']; ?>
							</span>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<?php
		if($SHOW_REPORT_RESULTS === true)
		{
		?>
		<div id='report_results' class='results_subdiv' style='display:none;'>
			<b><?php echo LangUtil::$pageTerms['MENU_REPORTRESULTS']; ?></b>
			<span id='report_results_load_progress'>
			&nbsp;&nbsp;&nbsp;
			<?php
			$page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_FETCHING']);
			?>
			</span>
			<br>
			<br>
			<div id='report_results_container'>
			
			<?php 
			/*
			
			*/
			?>
			</div>
		</div>
		<?php
		}
		?>


<form id='ajax_redirect' method='post' action='results_entry.php'>
	<input type='hidden' name='sid_redirect' id='sid_redirect' value=''></input>
	<input type='hidden' name='ajax_response' id='ajax_response' value=''></input>
</form>


</div>
</div>
<!-- END ROW-FLUID--> 
<?php
include("includes/scripts.php");
?>
<?php $script_elems->enableDatePicker();
$script_elems->enableJQueryForm();
$script_elems->enableJQueryValidate();
$script_elems->enableTableSorter();
$script_elems->enableLatencyRecord();
$script_elems->enableTokenInput();
?>
<script type='text/javascript'>
tableml = "";
unreported_fetched = false;

$(document).ready(function(){
	$('#cat_code').change( function() { get_test_types_bycat() });
	$('#worksheet_test_type').change( function() { reset_worksheet_custom_type() });
	get_test_types_bycat();
	$("#worksheet_results").hide();
	$('.results_subdiv').hide();
	right_load("pending_tests");
	<?php 
	if(isset($_REQUEST['ajax_response']))
	{
		#Rendering after Ajax response (workaround for dynamically loading JS via Ajax)
	?>
		$('#specimen_id').attr("value", "<?php echo $_REQUEST['sid_redirect'] ?>");
	<?php
	}
	else
	{
	?>
		$('#fetched_specimen').hide();
	<?php
	}
	?>
	$("#import_results").hide();
	$("#batch_results").hide();
	$('#resultfetch_attrib').change(function() {
	$('#specimen_id').focus();
	});
	$("input[name='is_blank']").change( function() {
		var is_blank = $("input[name='is_blank']:checked").val();
		if(is_blank == "Y")
			$('#num_rows_row').show();
		else
			$('#num_rows_row').hide();
	});
	<?php
	if($SHOW_REPORT_RESULTS === true)
	{
	?>
		load_unreported_results();
	<?php
	}
	?>
	//hide_worksheet_link();
});

function get_test_types_bycat()
{
	var cat_code = $('#cat_code').val();
	var location_code = <?php echo $_SESSION['lab_config_id']; ?>;
	$('#worksheet_test_type').load('ajax/tests_selectbycat.php?c='+cat_code+'&l='+location_code+'&all_no');
	reset_worksheet_custom_type();
}

function reset_worksheet_custom_type()
{
	$('#worksheet_custom_type').attr("value", "");
}

function toggle(elem_id)
{
	$('#'+elem_id).toggle();
}

function right_load(destn_div)
{
	//hide_worksheet_link();
	$('.results_subdiv').hide();
	$("#"+destn_div).show();
	$('#specimen_id').focus();
	$('.menu_option').removeClass('current_menu_option');
	$('#'+destn_div+'_menu').addClass('current_menu_option');
	$('#'+destn_div+'_subdiv_help').show();
	if(destn_div == "report_results")
	{
		load_unreported_results();
	}
	else if(destn_div == "pending_tests"){
		fetch_pending_specimens();
		
	}
	else if(destn_div == "pending_results"){
		fetch_pending_results();
	}
}

function load_unreported_results()
{
	if(unreported_fetched == false)
	{
		$('#report_results_load_progress').show();
		$('#report_results_container').load("ajax/results_getunreported.php", function() {
			$('#report_results_load_progress').hide();
		});
		unreported_fetched = true;
	}
}

function checkoruncheckall()
{
	if($('#check_all').attr("checked") == true)
	{
		$(".report_flag").attr("checked", "true");
	}
	else
	{
		$(".report_flag").removeAttr("checked");
	}
}

function hide_worksheet_link()
{
	document.getElementById("worksheet_link").innerHTML = "";
}

function hide_result_form(specimen_id)
{
	var target_div_id = "result_form_pane_"+specimen_id;
	$("#"+target_div_id).html("");
	$('#specimen_id').attr("value", "");
}

function fetch_specimen()
{
	var specimen_id = $('#specimen_id').val();
	specimen_id = specimen_id.replace(/[^a-z0-9 ]/gi,'');
	$('#fetch_progress_bar').show();
	<?php 
	#Used when Ajax response did not have JavaScript code included 
	?>
	var attrib = $('#resultfetch_attrib').val();
	var first_char =specimen_id.charAt(0);
	if(attrib==1 && isNaN(first_char)==false)
	{
		alert("Please enter a valid name.");
		return;
	}
	var url = 'ajax/result_entry_patient_dyn.php';
	$("#fetched_patient_entry").load(url, 
		{a: specimen_id, t: attrib}, 
		function() 
		{
			$('#fetch_progress_bar').hide();
			$("#fetched_specimen").show();
			$("#fetched_specimen").html("");
		}
	);
}

function fetch_pending_specimens()
{
	var url = 'ajax/result_entry_patient_dyn.php';
	$("#fetched_specimens_entry").load(url, 
		{a: '', t: 10}, 
		function() 
		{
			$('#fetch_progress_bar').hide();
			$("#fetched_specimen").show();
			$("#fetched_specimen").html("");
			handleDataTable(10);
		}
	);
}

function fetch_pending_results()
{
	var url = 'ajax/result_entry_patient_dyn.php';
	$("#fetched_pending_results_entry").load(url, 
		{a: '', t: 11}, 
		function() 
		{
			$('#fetch_progress_bar').hide();
			$("#fetched_specimen").show();
			$("#fetched_specimen").html("");
			handleDataTable(11);
		}
	);
}
function start_test(specimen_id)
{
	$('#'+specimen_id).show();
	var r=confirm("Start test?");
	if (r==true)
   	{
   		//Mark test as cancelled
  		var url = 'ajax/result_entry_patient_dyn.php';
		$("#fetched_pending_results_entry").load(url, 
		{a: specimen_id, t: 12}, 
		function() 
		{
			$('#fetch_progress_bar').hide();
			$('#'+specimen_id).hide();	
		}
	);
		
  	}
	else
  	{
  		//Cancel Starting test
  	}
}

function fetch_specimen2(specimen_id)
{
var pg=2;
	$('#fetch_progress_bar').show();
	var url = 'ajax/specimen_form_fetch.php';
	//var target_div = "fetch_specimen";
	$('.result_form_pane').html("");
	var target_div = "result_form_pane_"+specimen_id;
	$("#"+target_div).load(url, 
		{sid: specimen_id , page_id:pg}, 
		function() 
		{
			$('#fetch_progress_bar').hide();
			$("#fetched_specimen").show();
		}
	);
}

function verify_control_selection() {
	$('#control_testing_error').hide();
	var test_type_id = $('#verify_test_type_control').val();
	alert(test_type_id);
	//var result = $('#control_testing_form').value("controlTesting");
	var result = document.getElementById('controlTesting').value;
	alert(result);
	//alert(testName);
	if(test_type_id == "")
	{	
		$('#control_testing_error').show();
		return;
	}
	
	$('#control_result_done').show();
	
	//$('#control_testing_form').submit();
}

function toggle_form(form_id, checkbox_obj)
{
	if(checkbox_obj.checked == true)
	{
		$('#'+form_id+' :input').attr('disabled', 'disabled');
		checkbox_obj.disabled=false;
	}
	else
	{
		$('#'+form_id+' :input').removeAttr('disabled');
		checkbox_obj.disabled=false;
	}
}

function submit_forms(specimen_id)
{
	var form_id_csv = $('#form_id_list').val();
	var form_id_list = form_id_csv.split(",");
	$('.result_cancel_link').hide();
	$('.result_progress_spinner').show();
	//var target_div_id = "fetched_specimen";
	var target_div_id = "result_form_pane_"+specimen_id;
	for(var i = 0; i < form_id_list.length; i++)
	{
		if($('#'+form_id_list[i]+'_skip').is(':checked'))
		{
			continue;
		}
		var params = $('#'+form_id_list[i]).formSerialize();
			$.ajax({
			type: "POST",
			url: "ajax/result_add.php",
			data: params,
			success: function(msg) {
				$("#"+target_div_id).html(msg);
			}
		});
	}
	$('.result_progress_spinner').hide();
}

function get_batch_form()
{
	$('#batch_result_error').hide();
	tableml = "";
	var test_type_id = $('#batch_test_type').val();
	var date_to_array=$('#yyyy_to').val()+"-"+$('#mm_to').val()+"-"+$('#dd_to').val();
	var date_from_array=$('#yyyy_from').val()+"-"+$('#mm_from').val()+"-"+$('#dd_from').val();
	var table_id = 'batch_result_table';
	if(test_type_id == "")
	{	
		$('#batch_result_error').show();
		$('#batch_form_div').html("");
		return;
	}
	$('#batch_progress_form').show();
	$('#batch_form_div').load(
		"ajax/batch_results_form_fetch.php", 
		{ 
			t_type: test_type_id,
			date_to:date_to_array,
			date_from:date_from_array
		}
		,
		function (){
			<?php
			//Disabled table sorting, as batch entry forms are now aligned with worksheets
			//$('#'+table_id).tablesorter();
			?>
		}
	);
	$.ajax({
		type: "GET",
		url: "ajax/batch_results_form_row.php",
		data: "t_type="+test_type_id+"date_to="+date_to_array+"date_from="+date_from_array, 
		success : function(msg) {
            tableml = msg;
			$('#batch_progress_form').hide();
		}
	});
}

function get_verification_form()
{
	$('#verify_result_error').hide();
	var test_type_id = $('#verify_test_type').val();
	if(test_type_id == "")
	{	
		$('#verify_result_error').show();
		return;
	}
	$('#verify_progress_form').show();
	$('#verify_results_form').submit();
}

function get_worksheet()
{
	$('#worksheet_error').hide();
	var num_rows = $('#num_rows').val();
	if(isNaN(num_rows))
	{
		$('#num_rows').attr("value", "10");
	}
	var worksheet_id = $('#worksheet_custom_type').val();
	var test_type_id = $('#worksheet_test_type').val();
	if(worksheet_id == "" && test_type_id == "")
	{	
		$('#worksheet_error').show();
		return;
	}
	$('#worksheet_progress_form').show();
	$('#worksheet_form').submit();
	$('#worksheet_progress_form').hide();
}

function clear_batch_table()
{
	$('#batch_form_div').html("");
}

function submit_batch_form()
{
	$('#batch_submit_progress').show();
	$('#batch_submit_button').attr("disabled", "disabled");
	$('#batch_cancel_button').hide();
	$('#batch_form').submit();
}

function add_one_batch_row()
{
	var row_count = $('#batch_result_table tr').size();
	var row_html = "<tr valign='top'><td>"+row_count+"</td>"+tableml;
	$('#batch_result_table').append(row_html);
}

function add_five_batch_rows()
{
	for(var i = 0; i < 5; i++)
		add_one_batch_row();
}

function mark_reported()
{
	$('#report_results_progress_div').show();
	$('#report_results_form').ajaxSubmit({
		success: function() {
			$('#report_results_progress_div').hide();
			$('#report_results_form_div').hide();
			$('#report_results_confirm').show();
			unreported_fetched = false;
		}
	});
}

function show_more_pnum()
{
	$(".old_pnum_records").show();
	$("#show_more_pnum_link").hide();
}

function hide_result_confirmation(specimen_id)
{
	var target_div_id = "result_form_pane_"+specimen_id;
	$("#"+target_div_id).html("");
}
function update_numeric_remarks(test_type_id, count, patient_age, patient_sex)
{
	
 <?php # See ajax/specimen_form_fetch.php for field names ?>
	 var values_csv = "";
	 var remarks_input_id = "test_"+test_type_id+"_comments";
	 for(var i = 0; i < count; i++)
	 {
	 var input_id = "measure_"+test_type_id+"_"+i;
	 values_csv += $('#'+input_id).val()+"_";
	 }
	 var url_string = "ajax/fetch_remarks.php";
	values_csv = encodeURIComponent(values_csv);
	var data_string = "lid=<?php echo $_SESSION['lab_config_id']; ?>&ttype="+test_type_id+"&values_csv="+values_csv+"&patient_age="+patient_age+"&patient_sex"+patient_sex;
	 $.ajax({
	 type: "POST",
		 url: url_string,
		 data: data_string,
		 success: function(msg) {
		$("#"+remarks_input_id).attr("value", msg)
		 }
	 });

}


function update_remarks(test_type_id, count, patient_age, patient_sex)
{
	 <?php # See ajax/specimen_form_fetch.php for field names ?>
	 var values_csv = "";
	 var remarks_input_id = "test_"+test_type_id+"_comments";
	 for(var i = 0; i < count; i++)
	 {
	 var input_id = "measure_"+test_type_id+"_"+i;
	 values_csv += $('#'+input_id).val()+"_";
	 }
	 var url_string = "ajax/fetch_remarks.php";
	values_csv = encodeURIComponent(values_csv);
	var data_string = "lid=<?php echo $_SESSION['lab_config_id']; ?>&ttype="+test_type_id+"&values_csv="+values_csv+"&patient_age="+patient_age+"&patient_sex="+patient_sex;
	// var data_string = "lid=<?php echo $_SESSION['lab_config_id']; ?>&ttype="+test_type_id+"&values_csv="+values_csv;
	 $.ajax({
	 type: "POST",
		 url: url_string,
		 data: data_string,
		 success: function(msg) {
		$("#"+remarks_input_id).attr("value", msg)
		 }
	 });
}
</script>
<script>
function handleDataTable(table_id) {
    if (!jQuery().dataTable) {
        return;
    }
    (function($) {
    	/*
    	 * Function: fnGetColumnData
    	 * Purpose:  Return an array of table values from a particular column.
    	 * Returns:  array string: 1d data array 
    	 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
    	 *           int:iColumn - the id of the column to extract the data from
    	 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
    	 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
    	 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
    	 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
    	 */
    	$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
    		// check that we have a column id
    		if ( typeof iColumn == "undefined" ) return new Array();
    		
    		// by default we only wany unique data
    		if ( typeof bUnique == "undefined" ) bUnique = true;
    		
    		// by default we do want to only look at filtered data
    		if ( typeof bFiltered == "undefined" ) bFiltered = true;
    		
    		// by default we do not wany to include empty values
    		if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
    		
    		// list of rows which we're going to loop through
    		var aiRows;
    		
    		// use only filtered rows
    		if (bFiltered == true) aiRows = oSettings.aiDisplay; 
    		// use all rows
    		else aiRows = oSettings.aiDisplayMaster; // all row numbers

    		// set up data array	
    		var asResultData = new Array();
    		
    		for (var i=0,c=aiRows.length; i<c; i++) {
    			iRow = aiRows[i];
    			var aData = this.fnGetData(iRow);
    			var sValue = aData[iColumn];
    			
    			// ignore empty values?
    			if (bIgnoreEmpty == true && sValue.length == 0) continue;

    			// ignore unique values?
    			else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
    			
    			// else push the value onto the result data array
    			else asResultData.push(sValue);
    		}
    		
    		return asResultData;
    	}}(jQuery));


    	function fnCreateSelect( aData )
    	{
    		var r='<select><option value=""></option>', i, iLen=aData.length;
    		for ( i=0 ; i<iLen ; i++ )
    		{
    			r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
    		}
    		return r+'</select>';
    	}
    // begin first table
    var oTable = $('#'+table_id).dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sLengthMenu": "_MENU_ records per page",
            "oPaginate": {
                "sPrevious": "Prev",
                "sNext": "Next"
            }
        },
        "aoColumnDefs": [{
            'bSortable': false,
            'aTargets': [0]
        }],
    });

    jQuery('#'+table_id+' .group-checkable').change(function () {
        var set = jQuery(this).attr("data-set");
        var checked = jQuery(this).is(":checked");
        jQuery(set).each(function () {
            if (checked) {
                $(this).attr("checked", true);
            } else {
                $(this).attr("checked", false);
            }
        });
        jQuery.uniform.update(set);
    });

    jQuery('#'+table_id+'_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
    jQuery('#'+table_id+'_wrapper .dataTables_length select').addClass("m-wrap xsmall"); // modify table per page dropdown

    // begin second table
    $('#sample_2').dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sLengthMenu": "_MENU_ per page",
            "oPaginate": {
                "sPrevious": "Prev",
                "sNext": "Next"
            }
        },
        "aoColumnDefs": [{
            'bSortable': false,
            'aTargets': [0]
        }]
    });

    jQuery('#sample_2 .group-checkable').change(function () {
        var set = jQuery(this).attr("data-set");
        var checked = jQuery(this).is(":checked");
        jQuery(set).each(function () {
            if (checked) {
                $(this).attr("checked", true);
            } else {
                $(this).attr("checked", false);
            }
        });
        jQuery.uniform.update(set);
    });

    jQuery('#sample_2_wrapper .dataTables_filter input').addClass("m-wrap small"); // modify table search input
    jQuery('#sample_2_wrapper .dataTables_length select').addClass("m-wrap xsmall"); // modify table per page dropdown

    // begin: third table
    $('#sample_3').dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sLengthMenu": "_MENU_ per page",
            "oPaginate": {
                "sPrevious": "Prev",
                "sNext": "Next"
            }
        },
        "aoColumnDefs": [{
            'bSortable': false,
            'aTargets': [0]
        }]
    });

    jQuery('#sample_3 .group-checkable').change(function () {
        var set = jQuery(this).attr("data-set");
        var checked = jQuery(this).is(":checked");
        jQuery(set).each(function () {
            if (checked) {
                $(this).attr("checked", true);
            } else {
                $(this).attr("checked", false);
            }
        });
        jQuery.uniform.update(set);
    });

    jQuery('#sample_3_wrapper .dataTables_filter input').addClass("m-wrap small"); // modify table search input
    jQuery('#sample_3_wrapper .dataTables_length select').addClass("m-wrap xsmall"); // modify table per page dropdown
    /* Add a select menu for each TH element in the table footer */
// 	$("thead th").each( function ( i ) {
// 		this.innerHTML = fnCreateSelect( oTable.fnGetColumnData(i) );
// 		$('select', this).change( function () {
// 			oTable.fnFilter( $(this).val(), i );
// 		} );
// 	} );
}
</script>
<?php
$script_elems->bindEntertoClick("#specimen_id", "#fetch_specimen_button");
?>
<?php include("includes/footer.php"); ?>