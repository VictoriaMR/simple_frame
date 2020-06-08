var LOGIN = {
	init: function () {
		$('#login-btn').on('click', function() {
			var msg = '';
			$('#login-error').hide();
			$(this).parent('form').find('input:visible').each(function(){
				var name = $(this).attr('name');
				console.log($(this).val())
				console.log(name, VERIFY[name]($(this).val()))
				if (!VERIFY[name]($(this).val())) {
					switch (name) {
						case 'phone':
							msg = '手机号码格式不正确';
							break;
						case 'password':
							msg = '密码格式不正确';
							break;
						default:
							msg = '输入错误';
							break;
					}
					return false;
				}
			});

			if (msg != '') {
				$('#login-error').show().find('#login-error-msg').text(msg);
				return false;
			}

			API.post(ADMIN_URL+'login/login', $(this).parent('form').serializeArray(), function(res) {
				if (res.code == 200) {
					window.location.reload();
				} else {
					$('#login-error').show().find('#login-error-msg').text(res.msg);
				}
			})
		});
	}
};