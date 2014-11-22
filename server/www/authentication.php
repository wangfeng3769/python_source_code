

	<?php include 'header.html';?>
<iframe id="vFormSubmitIfrm" name="vFormSubmitIfrm" src="false" style="display: none" onload="ifrmLoader();"></iframe>
<div class="auth">
<div class="zhuce-1">注册成功</div>
<div class="zhuce-1"><p class="zhuce-txt-1" style="width:680px">恭喜您，注册成功！如果您有驾照，请完善以下信息进行实名认证。</p><a href="#" class="anniu-1">我没有驾照</a></div>
<form id="vForm" method="post" target="vFormSubmitIfrm" action="/client/index.php?class=UserManager&item=saveVerificationImgs" enctype="multipart/form-data">
<div class="renzheng-1" style="height:260px"><p class="zhuce-txt-1" style="width:430px;">第一步：请上传持身份证正面头部照片</p>
<p class="zhuce-txt" style="width:150px">请注意</p>
<p class="zhuce-txt-3" style="width:300px">·照片信息必须真实有效</p>
<p class="zhuce-txt-3" style="width:300px">·照片须免冠，未化妆</p>
<p class="zhuce-txt-3" style="width:300px">·手持证件人的五官必须完整清晰</p>
<p class="zhuce-txt-3" style="width:300px">·文件为源文件（未经任何软件编辑）</p>
<p class="zhuce-txt-3" style="width:300px">图片规格必须大于400x400,小于4000x4000</p>
</div>
<div class="renzheng"><img id="vIdImg" src="img/pic.jpg"/><p class="denglu-1-2" style="width:360px"><input type="file" id="vFileId" name="id" onchange="a.loadImg('file','img1');" /></p></div>


<div class="renzheng-1" style="height:200px"><p class="zhuce-txt-1" style="width:500px;">第二步：请上传驾驶证、学生证和身份证背面照</p>
<p class="zhuce-txt" style="width:150px">请注意</p>
<p class="zhuce-txt-3" style="width:300px">·照片信息必须真实有效</p>
<p class="zhuce-txt-3" style="width:300px">·文件为源文件（未经任何软件编辑）</p></div>
<div class="renzheng"><img id="vOthersImg" src="img/pic-2.jpg"/><p class="denglu-1-2" style="width:360px"><input type="file" id="vFileOthers" name="others" onchange="a.loadImg('file','img1');" /></p></div>
<div class="denglu-1-2" style="width:960px"><a id="vBtnSubmit" href="javascript:submitFile();" class="anniu-5" style="margin:20px 0 0 400px">提交认证</a></div>
</div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		$("#vFileId").change(showIdImg);
		$("#vFileOthers").change(showOthersImg);
	});

	function showIdImg() {
		loadImg("vFileId", "vIdImg");
	}

	function showOthersImg() {
		loadImg("vFileOthers", "vOthersImg");
	}
	
	function submitFile(){
		document.getElementById( "vForm" ).submit();
	}

	function loadImg(sourceTag, targetTag) {
		if (window.navigator.userAgent.indexOf("MSIE") >= 1) {
			var file = document.getElementById(sourceTag);
			document.getElementById(targetTag).src = file.value;
		} else {
			var files = document.getElementById(sourceTag);
			var imgTarget = document.getElementById(targetTag);
			var file = files.files[0];
			var reader = new FileReader();

			reader.onload = (function(imgDom) {
				return function(e) {
					imgDom.src = e.target.result;
				}
			})(imgTarget);

			reader.readAsDataURL(file);
		}

	}

	function ifrmLoader() {
		var ret = vFormSubmitIfrm.document.body.innerHTML;
		SA.parseRet(ret, function() {
			alert("提交审核信息成功。");
			//window.location = 'index.php';
		});
	}
</script>
<?php include 'footer.html';?>
</body>
</html>
