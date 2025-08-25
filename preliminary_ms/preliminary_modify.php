<?php

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;


//載入公用函數
@include_once '/website/include/pub_function.php';

//連結資料
@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction("processform");
function processform($aFormValues){

	$objResponse = new xajaxResponse();
	
	$web_id				= trim($aFormValues['web_id']);
	$auto_seq			= trim($aFormValues['auto_seq']);
	
	SaveValue($aFormValues);
	
	$objResponse->script("setSave();");
	$objResponse->script("parent.myDraw();");

	$objResponse->script("art.dialog.tips('已存檔!',1);");
	$objResponse->script("parent.$.fancybox.close();");
	$objResponse->script("parent.eModal.close();");
		
	
	return $objResponse;
}


$xajax->registerFunction("SaveValue");
function SaveValue($aFormValues){

	$objResponse = new xajaxResponse();
	
		//進行存檔動作
		$site_db				= trim($aFormValues['site_db']);
		$auto_seq				= trim($aFormValues['auto_seq']);
		$memberID				= trim($aFormValues['memberID']);
		$status1				= trim($aFormValues['status1']);
		$status2				= trim($aFormValues['status2']);
		$Handler				= trim($aFormValues['Handler']);
		$buildings				= trim($aFormValues['buildings']);
		$first_review_date		= trim($aFormValues['first_review_date']);
		$estimated_return_date	= trim($aFormValues['estimated_return_date']);
		$preliminary_status		= trim($aFormValues['preliminary_status']);
		$remark					= trim($aFormValues['remark']);
		//$confirm2				= trim($aFormValues['confirm2']);
		
		//存入info實體資料庫中
		$mDB = "";
		$mDB = new MywebDB();

		$Qry="UPDATE CaseManagement set
				 status1			= '$status1'
				,status2			= '$status2'
				,Handler			= '$Handler'
				,buildings			= '$buildings'
				,first_review_date	= '$first_review_date'
				,estimated_return_date	= '$estimated_return_date'
				,preliminary_status	= '$preliminary_status'
				,remark				= '$remark'
				,makeby2			= '$memberID'
				,last_modify2		= now()
				where auto_seq = '$auto_seq'";
				
		$mDB->query($Qry);
        $mDB->remove();

		
	return $objResponse;
}

$xajax->processRequest();



$auto_seq = $_GET['auto_seq'];
$fm = $_GET['fm'];

$mess_title = $title;

//$pro_id = "com";


$mDB = "";
$mDB = new MywebDB();
$Qry="SELECT a.*,b.employee_name,c.engineering_name,d.builder_name,e.contractor_name,f.company_name,f.short_name FROM CaseManagement a
LEFT JOIN employee b ON b.employee_id = a.Handler
LEFT JOIN construction c ON c.construction_id = a.construction_id
LEFT JOIN builder d ON d.builder_id = a.builder_id
LEFT JOIN contractor e ON e.contractor_id = a.contractor_id
LEFT JOIN company f ON f.company_id = a.company_id
WHERE a.auto_seq = '$auto_seq'";
$mDB->query($Qry);
$total = $mDB->rowCount();
if ($total > 0) {
    //已找到符合資料
	$row=$mDB->fetchRow(2);
	$status1 = $row['status1'];
	$status2 = $row['status2'];
	$region = $row['region'];
	$case_id = $row['case_id'];
	$construction_id = $row['construction_id'];
	$engineering_name = $row['engineering_name'];
	$builder_id = $row['builder_id'];
	$builder_name = $row['builder_name'];
	$contractor_id = $row['contractor_id'];
	$contractor_name = $row['contractor_name'];
	$contact = $row['contact'];
	$site_location = $row['site_location'];
	$county = $row['county'];
	$town = $row['town'];
	$zipcode = $row['zipcode'];
	$address = $row['address'];
	$ContractingModel = $row['ContractingModel'];
	$Handler = $row['Handler'];
	$Handler_name = $row['employee_name'];
	$buildings = $row['buildings'];
	$first_review_date = $row['first_review_date'];
	$estimated_return_date = $row['estimated_return_date'];
	$preliminary_status = $row['preliminary_status'];
	$remark = $row['remark'];
	$company_id = $row['company_id'];
	$company_name = $row['short_name'];
	if (empty($company_name))
		$company_name = $row['company_name'];

	$makeby2 = $row['makeby2'];
	$last_modify2 = $row['last_modify2'];


}


$getsmallclass = "/smarty/templates/$site_db/$templates/sub_modal/base/pjclass_ms/getsmallclass.php";
$getmainclass = "/smarty/templates/$site_db/$templates/sub_modal/base/pjclass_ms/getmainclass.php";


$pro_id = "CaseManagement";
//載入主類別選項
$Qry="select caption from pjclass where pro_id = '$pro_id' and small_class = '0' order by orderby";
$mDB->query($Qry);
$select_status1 = "";
$select_status1 .= "<option></option>";

if ($mDB->rowCount() > 0) {
    while ($row=$mDB->fetchRow(2)) {
		$mc_caption = $row['caption'];
		$select_status1 .= "<option value=\"$mc_caption\" ".mySelect($mc_caption,$status1).">$mc_caption</option>";
	}
}
//檢查並設定細類
//先取出 caption () 的 main_class 值
$m_row = getkeyvalue2($site_db."_info","pjclass","pro_id = '$pro_id' and small_class = '0' and caption = '$status1'","main_class");
$main_class_seq = $m_row['main_class'];
//從資料庫中讀取主類別資料
$Qry="select caption from pjclass where pro_id = '$pro_id' and main_class = '$main_class_seq' and small_class <> '0' order by orderby";
$select_status2 = "";
$select_status2 .= "<option></option>";
$mDB->query($Qry);
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$sc_caption = $row['caption'];
		$select_status2 .= "<option value=\"$sc_caption\" ".mySelect($sc_caption,$status2).">$sc_caption</option>";
	}
}	


$mDB->remove();


$show_savebtn=<<<EOT
<div class="btn-group vbottom" role="group" style="margin-top:5px;">
	<button id="save" class="btn btn-primary" type="button" onclick="CheckValue(this.form);" style="padding: 5px 15px;"><i class="bi bi-check-circle"></i>&nbsp;存檔</button>
	<button id="cancel" class="btn btn-secondary display_none" type="button" onclick="setCancel();" style="padding: 5px 15px;"><i class="bi bi-x-circle"></i>&nbsp;取消</button>
	<button id="close" class="btn btn-danger" type="button" onclick="parent.myDraw();parent.$.fancybox.close();" style="padding: 5px 15px;"><i class="bi bi-power"></i>&nbsp;關閉</button>
</div>
EOT;


if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = 0;
	
$style_css=<<<EOT
<style>

.card_full {
    width: 100%;
	height: 100vh;
}

#full {
    width: 100%;
	height: 100vh;
}

#info_container {
	width: 900px !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:200px;display: none;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:680px;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

.code_class {
	width:200px;
	text-align:right;
	padding:0 10px 0 0;
}

.custom-pointer {
  cursor: pointer;
}

</style>

EOT;

} else {
	$isMobile = 1;

$style_css=<<<EOT
<style>

.card_full {
    width: 100vw;
	height: 100vh;
}

#full {
    width: 100vw;
	height: 100vh;
}

#info_container {
	width: 100% !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:15px 10px 0 0;vertical-align: top;}
.field_div2 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}

.code_class {
	width:auto;
	text-align:left;
	padding:0 10px 0 0;
}

</style>
EOT;

}


$show_center=<<<EOT

$style_css

<div class="card card_full">
	<div class="card-header text-bg-info">
		<div class="size14 weight float-start" style="margin-top: 5px;">
			$mess_title
		</div>
		<div class="float-end" style="margin-top: -5px;">
			$show_savebtn
		</div>
	</div>
	<div id="full" class="card-body data-overlayscrollbars-initialize">
		<div id="info_container">
			<form method="post" id="modifyForm" name="modifyForm" enctype="multipart/form-data" action="javascript:void(null);">
			<div class="w-100 mb-5">
				<div class="field_container3">
					<div>
						<div class="field_div1">狀態:</div> 
						<div class="field_div2">
							<div class="inline text-nowrap mb-1">
								(1):
								<select id="status1" name="status1" style="width:150px;" onchange="setEdit();">
									$select_status1
								</select>
							</div>
							<div class="inline text-nowrap mb-1">
								(2):
								<select id="status2" name="status2" style="width:150px;">
									$select_status2
								</select>
							</div>
						</div> 
					</div>
					<div>
						<div class="field_div2">
							<div class="my-1">
								<div class="inline code_class">工程案件:</div>
								<div class="inline blue weight me-2">$case_id</div>
								<div class="inline blue weight me-2">$region</div>
								<div class="inline blue weight me-2">$construction_id</div>
								<div class="inline"><i id="expand" class="bi bi-caret-down-fill gray custom-pointer" title="展開"></i></div>
							</div>
							<div id="content" class="w-100 display_none">
								<div class="mytable w-100">
									<div class="myrow">
										<div class="mycell code_class">上包-建商名稱:</div>
										<div class="mycell blue weight">
											<div class="inline blue weight">$builder_name</div>
											<div class="inline size08 gray">$builder_id</div>
										</div>
									</div>
									<div class="myrow">
										<div class="mycell code_class">上包-營造廠名稱:</div>
										<div class="mycell blue weight">
											<div class="inline blue weight">$contractor_name</div>
											<div class="inline size08 gray">$contractor_id</div>
										</div>
									</div>
									<div class="myrow">
										<div class="mycell code_class">連絡人:</div>
										<div class="mycell blue weight">$contact</div>
									</div>
									<div class="myrow">
										<div class="mycell code_class">案場位置:</div>
										<div class="mycell blue weight">{$zipcode}{$county}{$town}{$address}</div>
									</div>
									<!--
									<div class="myrow">
										<div class="mycell code_class">承攬模式:</div>
										<div class="mycell blue weight">$ContractingModel</div>
									</div>
									<div class="myrow">
										<div class="mycell code_class">所屬公司:</div>
										<div class="mycell blue weight">
											<div class="inline blue weight">$company_name</div>
											<div class="inline size08 gray">$company_id</div>
										</div>
									</div>
									-->
								</div>
							</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">經辦人員:</div> 
						<div class="field_div2">
							<div class="input-group text-nowrap" style="width:100%;max-width:450px;">
								<input readonly type="text" class="form-control w-25" id="Handler" name="Handler" aria-describedby="Handler_addon" value="$Handler"/>
								<input readonly type="text" class="form-control w-50" id="Handler_name" name="Handler_name"  value="$Handler_name"/>
								<button class="btn btn-outline-secondary w-25" type="button" id="Handler_addon" onclick="openfancybox_edit('/index.php?ch=ch_employee&fm=$fm',800,'96%','');">選擇員工</button>
							</div>
						</div>
					</div>
					<div>
						<div class="field_div1">建物棟數:</div> 
						<div class="field_div2">
							<input type="text" class="inputtext" id="buildings" name="buildings" size="20" maxlength="160" style="width:100%;max-width:450px;" value="$buildings" onchange="setEdit();"/>
						</div> 
					</div>
					<div>
						<div class="field_div1">初評發送日期:</div> 
						<div class="field_div2">
							<div class="input-group" id="first_review_date" style="width:100%;max-width:250px;">
								<input type="text" class="form-control" name="first_review_date" placeholder="請輸入初評發送日期" aria-describedby="first_review_date" value="$first_review_date">
								<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#first_review_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
							</div>
							<script type="text/javascript">
								$(function () {
									$('#first_review_date').datetimepicker({
										locale: 'zh-tw'
										,format:"YYYY-MM-DD"
										,allowInputToggle: true
									});
								});
							</script>
						</div> 
					</div>
					<div>
						<div class="field_div1">預計回饋日期:</div> 
						<div class="field_div2">
							<div class="input-group" id="estimated_return_date" style="width:100%;max-width:250px;">
								<input type="text" class="form-control" name="estimated_return_date" placeholder="請輸入預計回饋日期" aria-describedby="estimated_return_date" value="$estimated_return_date">
								<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#estimated_return_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
							</div>
							<script type="text/javascript">
								$(function () {
									$('#estimated_return_date').datetimepicker({
										locale: 'zh-tw'
										,format:"YYYY-MM-DD"
										,allowInputToggle: true
									});
								});
							</script>
						</div> 
					</div>
					<div>
						<div class="field_div1">初評狀態:</div> 
						<div class="field_div2">
							<input type="text" class="inputtext" id="preliminary_status" name="preliminary_status" size="20" maxlength="160" style="width:100%;max-width:450px;" value="$preliminary_status" onchange="setEdit();"/>
						</div> 
					</div>
					<div>
						<div class="field_div1">備註:</div> 
						<div class="field_div2">
							<input type="text" class="inputtext" id="remark" name="remark" size="20" maxlength="160" style="width:100%;max-width:450px;" value="$remark" onchange="setEdit();"/>
						</div> 
					</div>
					<!--
					<div>
						<div class="field_div1">設定:</div> 
						<div class="field_div2 pt-3">
							<input type="checkbox" class="inputtext" name="confirm2" id="confirm2" value="Y" $m_confirm2 />
							<label for="confirm2" class="red">確認</label>
						</div>
					</div>
					-->
					<div>
						<input type="hidden" name="fm" value="$fm" />
						<input type="hidden" name="site_db" value="$site_db" />
						<input type="hidden" name="auto_seq" value="$auto_seq" />
						<input type="hidden" name="memberID" value="$memberID" />
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<script>

function CheckValue(thisform) {
	xajax_processform(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function SaveValue(thisform) {
	xajax_SaveValue(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function setEdit() {
	$('#close', window.document).addClass("display_none");
	$('#cancel', window.document).removeClass("display_none");
}

function setCancel() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
	document.forms[0].reset();
}

function setSave() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
}

$(document).ready(function () {
  $("#expand").on("click", function () {
    // 切換展開/摺疊內容
    let content = $("#content"); // 假設展開的內容有 id 為 content
    content.toggleClass("display_none");

    // 切換圖示方向
    $(this).toggleClass("bi-caret-down-fill bi-caret-up-fill");

    // 更新 title 提示文字
    let newTitle = content.hasClass("display_none") ? "展開" : "摺疊";
    $(this).attr("title", newTitle);
  });
});



function getSelectVal(){ 
	$("option",status2).remove(); //清空原有的選項
	var main_class_val = $("#status1").val();
    $.getJSON('$getsmallclass',{main_class:main_class_val,site_db:'$site_db',pro_id:'$pro_id'},function(json){ 
        var small_class = $("#status2"); 
        var option = "<option></option>";
		small_class.append(option);
        $.each(json,function(index,array){ 
			option = "<option value='"+array['caption']+"'>"+array['caption']+"</option>"; 
            small_class.append(option); 
        }); 
    });
}

$(function(){ 
    $("#status1").change(function(){ 
        getSelectVal(); 
    }); 
});


//更新主類別
function getMainSelectVal(){ 
    $.getJSON("$getmainclass",{site_db:'$site_db',pro_id:'$pro_id'},function(json){ 
        var main_class = $("#status1"); 
		var last_option = main_class.val();
        $("option",status1).remove(); //清空原有的選項
        var option = "<option></option>";
		main_class.append(option);
        $.each(json,function(index,array){
			if (array['caption'] == last_option)
				option = "<option value='"+array['caption']+"' selected>"+array['caption']+"</option>"; 
			else
				option = "<option value='"+array['caption']+"'>"+array['caption']+"</option>"; 
            main_class.append(option); 
        }); 
    }); 
}


$(document).ready(async function() {
	//等待其他資源載入完成，此方式適用大部份瀏覽器
	await new Promise(resolve => setTimeout(resolve, 100));
	$('#status1').focus();
});

</script>

EOT;

?>