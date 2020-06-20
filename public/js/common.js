var API = {
	get: function(url, param, callback) {
		$.get(url, param, function(res) {
			if (callback) callback(res);
		}, 'json');
	},
	post: function(url, param, callback) {
		$.ajaxSetup({
	  		async: true
	  	});
		$.post(url, param, function(res) {
			if (callback) callback(res);
		}, 'json');
	},
};

var VERIFY = {
	phone: function (phone) {
		var reg = /^1[3456789]\d{9}$/;
		return VERIFY.check(phone, reg);
	},
	email: function (email) {
		var reg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
		return VERIFY.check(email, reg);
	},
	password: function (password) {
		var reg = /^[0-9A-Za-z]{6,}/;
		return VERIFY.check(password, reg);
	},
	code: function(code) {
		var reg = /^\d{4,}/;
		return VERIFY.check(code, reg);
	},
	check: function(input, reg) {
		input = input.trim();
		if (input == '') return false;
		return reg.test(input);
	}
};