function ServerAgent() {

	var me = this;

	this.SERVERAPI = "/client/index.php";

	this.get = function(url, retFn, data) {
		$.post(url, function(data, textStatus, jqXHR) {
			me.retFn(data, retFn);
		}, data, 'json');
	}

	this.retFn = function(retInfo, retFn) {
		switch( retInfo.errno ) {
			case 2000:
				if ("function" == typeof (retFn )) {
					retFn(retInfo.content);
				}
				break;
			case 4060:
			case 4020 :
				alert("登录超时，请重新登录");
				break;
			default:
				alert(retInfo.errstr);
		}
	}

	this.parseRet = function(str, retFn) {
		var obj = eval('(' + str + ')');

		me.retFn(obj, retFn);
	}
}

var SA = new ServerAgent();
