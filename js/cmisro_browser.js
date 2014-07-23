"use strict";
var CMISRO_BROWSER = {
	handleSelection: function (input_id, cmis_id) {
		window.opener.document.getElementById(input_id).value=cmis_id;
		self.close();
	}
}
