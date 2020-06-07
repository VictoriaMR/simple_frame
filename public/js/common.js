var API = {
	get: function(url, param, callback) {
		$.get(url, param, function(res) {
			if (callback) callback();
		}, 'json');
	},
	post: function(url, param, callback) {
		$.post(url, param, function(res) {
			if (callback) callback();
		}, 'json');
	},
};

var VERIFY = {
	phone: function(phone) {
		return this.check(phone);
	},
	password: function(password) {
		return this.check(password);
	},
	check: function(code, reg) {
		if (code == '') return false;
		return true;
	}
};