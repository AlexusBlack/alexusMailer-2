</script>
</head>
<body>
	
<div class="modal fade" id="univarsalUpload">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>%attachfile%</h3>
			</div>

			<div class="modal-body">
				<iframe src="%PHP_SELF%?upload_universal" width="260px" height="80px" frameborder="0"></iframe>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal">%close%</a>
				<a href="#" class="btn btn-primary" onclick="UploadClickHandler(this);return false">%upload%</a>
			</div>
		</div>
	</div>
</div>
	
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="navbar navbar-default">
			    <div class="container-fluid">
				    <a class="navbar-brand prime-button" href="<?php print SERVICEMODE?"/".str_replace("ru/", "", $translation->current()."/"):"#prime"; ?>" id="hello">%name% <?php echo VERSION;?></a>
				    <ul class="nav navbar-nav">
					    <li><a href="#prime" id="prime"><img alt="%main%" src="data:image/png;base64,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"></a></li>
					    <li><a href="#help" id="help"><img alt="%help%" src="data:image/png;base64,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"></a></li>
					    <li><a href="#settings" id="settings"><img alt="%settings%" src="data:image/png;base64,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"></a></li>
				    </ul>
				    <ul class="nav navbar-nav pull-right">
				    <?php if(SERVICEMODE):?>
				    	<li><a href="#" id="sendlimit" onclick="checkOutOfLimit();return false">%sendavailable%</a></li>
				    	<li><a href="#" id="sendlimit_check" onclick="checkOutOfLimit();return false"><i class="glyphicon glyphicon-refresh" style="color:white;"></i></a></li>
				    <?php endif; ?>
				    	<li><?php if(SERVICEMODE):?><a href="/demo/"><?php else: ?><a href="#" onclick="setLang('ru');return false"><?php endif; ?>
				    		<img alt="русский" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAMAAADXqc3KAAABSlBMVEUAAAD///8AAQEAAQEAAQEAAQEAAQHT09PX19fa2trc3Nze3t7f39/Ozs719fX5+fn8/Pz9/f3+/v7+/v7+/v7+/v7+/v7ExMTz8/P4+Pj7+/v9/f3+/v7Dw8Py8vL4+Pj8/Pz9/f3CwsLw8PD39/f5+fn6+vr7+/sCAsA9Pe08PPU8PPc8PPg8PPkCAr8zM+syMvIyMvUyMvYxMfYCAr0oKOgnJ/AnJ/MnJ/QnJ/QCArslJeYnJ+4nJ/AnJ/EmJvICAroVFeEVFeoVFe0VFe4VFe+4AgLgERHoERHqERHrEBDsEBC2AgLdDQ3lDAzoDAzqDQ21AgLZCAjjCAjmCQnnCQnoCAizAgLXBQXfBQXjBQXkBQXlBQWzBQXVBwfbCAjhEBDjFhbmGxvlHBzmHx/mICDmISG4DAy5EBC9ERHAERHCERHDERHEERHpaBTVAAAAB3RSTlMAAAQMECQwISWBWgAAAGZJREFUKFPNjUEKgDAMBHdr0kP//1dBxdhIW1qoF0FwjjNkA3wPg4JOM+ZgF0ZWqnUOYRoPSlwF+WLXIWwxbwmUvtYHtTvE6dQvAnpK0HdTNVolh9Rsd2DgEsbPpZ4MnAabaic8eFyiFV6rXRgALgAAAABJRU5ErkJggg==" />
				    	</a></li>
				    	<li><?php if(SERVICEMODE):?><a href="/en/demo/"><?php else: ?><a href="#" onclick="setLang('en');return false"><?php endif; ?>
				    		<img alt="english" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAEM0lEQVRIie2Ua0jVZxzHSxJ6YaBBCFFYYtpeNJagjqaDQalbi+zFaGkT5yzWiiajTYsZTXIt87Z5qdkU04aF9ww6HrVjamfaIW/pqSyPnTpXtSzzkqn77Hn+madYbzaKvdkfvi/O7/L5PpffeebN+//777/58xfU79hBl05Hi66LiH3FeH+UiffGLKHsORkM/dStXYva25u+7m7e257/Qj5L6QnbU0R9Rjbn/f3RZmUh2dLApWb3boxpaYz29zM9PU3LNRvHCjtIPtVBSlEHaac7mZn5E3tODraMDGYmJjhZrldysuZoQTuNrXcYLC3FlJ7OiNlMW1ubNHCRBq6+m5O5o2niVkwMQxUVAjaDwfyI2JTLbI2vVSQNDFFR3N66lenRUb460qTEv/yxEX1TJ8bYWOyFhTwZG8M+NEzQp2nSwFUxcHrrO3YmqvmjoQtjfDz3Dh3i6fAwY+OT/FzcSdg3KrGzGW6Hh9MbFsbU48d8kdjAkfyr2MoqMERHMyKOeGpqivbrNvYcqUMyXzD4lpBdpQTvqiTz93bMefn0btvGoytXlKb61rs8fTrFzS1buB4czOTDh9TVdWPcvx9jXBwTAwM8mZwkr6JbYUiWZDoMVu8jKKqYwM9LCIwuJTKhlpsXLnFdrNacnc2k2PakAOhDQugODGTiwQN6IyKwFRQocaPlITsPX1R6JUOyJHPO4KJGw+DgICMjI4wJ2Pj4+CvVGRREu5ikUVH7qrzslQzJ0gjmnEHuokU0rl5Ni48PV4R0/1KyVzIk64STk8MgR/zQrFqFdragVRaLef8nap3tlQzJOiEWPWegVqsxGo0MiMu6f/8+D8QZD4spelFDd+/StGQJWk/Pv+WkZI/slQzJqhFMxyX7fI3bulTcAn8RyuSDmGI69EaGhoa4kZREi58fA+JP2ODqStPy5Up80GqlR4z0ZXEc2oISAiKKlF6FIViS6TDw3ssi/6O4BKQRl1qL1WbH3NNDa2gorRs38lueGmtfH/Vi2w1Ll2K3D5BfplNW21deTqOXFx1x8ez+4bzCkCzJfMnAY306Zao2LBYLvWfO0LBiBVcPHWbz3hI8P/wVy61b1C5cyEVxTFarjYV+6WzfX4mh34RZr0crRrhZTFlR7jnc30952eDdLYnioevE0NuLTrxLGnFRlcfPsnxDNs6+KXgE53Dvxg1Uzs7ULl6MyWRW4lI+m3Kpbe4RMRPtCQlccHdH/VMqb4cedBhUV1fTrlJR4+uLNjKSg8lVCnyZkIQHhJ/CJHagWbmSS2vWYDZbWBF6XMkpNSE5JJ/UKJd7raqKc+LI8j/e5DAoOXCAXA8PTscnsO6TY7j5fT+rBEWrNiTRKKaiwM2NwmXLaGpunss907P69Z+lU3FOhUqYFIq7e27gIuQl9M5rltfz53rBrInra5aLwgbeqN64wV+Rl1Pcwvw+zwAAAABJRU5ErkJggg==" />
				    	</a></li>
				    </ul>
				    
			    </div>
			</div>
			
		</div>	
	</div>
		
	<div class="row section-screen" id="prime-screen">
		<div class="col-md-3">
			<div id="console" class="well" style="text-align:center;">%status%</div>
			<div id="ext-console" class="well"></div>	
		</div>
		<div class="col-md-9">
			<div class="progress" id="progressbar">
		    	<div class="bar bar-warning" style="width: 0%;"></div>
		    	<div class="barcounter">0/0</div>
		    </div>
		    <div class="well status">
			    <b>%status%: <span class="label label-warning">%status-idle%</span></b>
		    	<div class="controls">
		    		<button class="btn btn-default" disabled="disabled" id="ResumeSendMail" onclick="ResumeSendMail()"><i class="glyphicon glyphicon-play"></i> %process-resume%</button>
		    		<button class="btn btn-default" disabled="disabled" id="PauseSendMail" onclick="PauseSendMail()"><i class="glyphicon glyphicon-pause"></i> %process-pause%</button> 
		    		<button class="btn btn-default" disabled="disabled" id="StopSendMail" onclick="StopSendMail()"><i class="glyphicon glyphicon-stop"></i> %process-cancel%</button>  
		    	</div>
		    </div>
		    <form class="form-horizontal">
				<div class="form-group">
					<label class="control-label col-sm-2" for="inputEmail">%recipient% [TO-EMAIL]</label>
					<div class="col-sm-7 input-group">
						<textarea name="to" id="to" class="form-control input-xlarge txtinput" placeholder="vasya@yandex.ru"></textarea>
						<span class="input-group-addon" onclick="uploadListField('#to');return false" style="cursor:pointer;">%upload%</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="inputEmail">%fromname% [FROM-NAME]</label>
					<div class="col-sm-7 input-group">
						<textarea name="fromname" id="fromname" class="form-control input-xlarge txtinput" placeholder="Bill Gates"></textarea>
						<span class="input-group-addon" onclick="uploadListField('#fromname');return false" style="cursor:pointer;">%upload%</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="inputEmail">%frommail% [FROM-EMAIL]</label>
					<div class="col-sm-7 input-group">
						<textarea name="frommail" id="frommail" class="form-control input-xlarge txtinput" placeholder="bill@microsoft.com"></textarea>
						<span class="input-group-addon" onclick="uploadListField('#frommail');return false" style="cursor:pointer;">%upload%</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="inputEmail">%replymail% [REPLY-EMAIL]</label>
					<div class="col-sm-7 input-group">
						<textarea name="replymail" id="replymail" class="form-control input-xlarge txtinput" placeholder="my@email.com"></textarea>
						<span class="input-group-addon" onclick="uploadListField('#replymail');return false" style="cursor:pointer;">%upload%</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="inputEmail">%subject% [THEME]</label>
					<div class="col-sm-7 input-group">
						<textarea name="tema" id="tema" class="form-control input-xlarge txtinput" placeholder="%subject_example%"></textarea>
						<span class="input-group-addon" onclick="uploadListField('#tema');return false" style="cursor:pointer;">%upload%</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="inputEmail">%addfield%</label>
					<div class="col-sm-7 input-group">
						<span class="input-group-addon">[ADD0]</span>
						<input type="text" name="additional" id="additional0" class="form-control input-xlarge txtinput additional" placeholder="%addfield2%"></textarea>
						<span class="input-group-addon"><span class="addfield" onclick="AddField(this)">+</span></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="inputEmail">%mailtype%</label>
					<div class="col-sm-7 input-group">
						<select name="type" id="type" class="form-control">
							<option value="html">html - %withformating%</option>
							<option value="htmle">html(e) - %htmle%</option>
							<option value="text">text - %plaintext%</option>
						</select>
					</div>
				</div>
				<?php if(SERVICEMODE):?>
				<div class="form-group">
					<label class="control-label col-sm-2" for="inputEmail">%captcha%</label>
					<div class="col-sm-7 input-group">
						<input type="text" name="captcha_code" id="captcha_code" class="form-control input-xlarge txtinput">
						<img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />
						<a href="#" class="btn btn-lg btn-default" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">
							<i class="glyphicon glyphicon-refresh"></i>
						</a>
					</div>
				</div>
				<?php endif;?>
				<div style="resize:vertical;" id="resizableTextContainer">
				<textarea name="text" id="text" style="width:100%;height:200px;" class="form-control"></textarea>
				</div>
				<button class="btn btn-default" onclick="showAttachUpload();return false"><i class="glyphicon glyphicon-plus"></i> %attachfile%</button>
				<span id="attachedFiles"></span>
			</form>
			<div class="pull-left">
				<button class="btn btn-lg btn-default" onclick="SaveTemplate()">%save%</a>
				<button class="btn btn-lg btn-default" onclick="uploadTemplate()">%load%</a>
			</div>
			<div class="pull-right">
				<button class="btn btn-lg btn-primary" id="mainSendButton" onclick="Send()">%send%</a>
				<button class="btn btn-lg btn-default" onclick="Preview()">%preview%</a>
			</div>
		</div>
	</div>

	<div class="row section-screen" id="settings-screen">
		<div class="col-md-12">
			<div class="well">
				<h2>%settings%</h2>
				<div class="btn-group" id="saveSettings">
				  	<button type="button" class="btn btn-default" onclick="saveSettings()" <?php if(SERVICEMODE):?>disabled<?php endif;?>>%save%</button>	
				  	<div class="btn-group">
				  	  	<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" <?php if(SERVICEMODE):?>disabled<?php endif;?>>
				  	  	  	<span class="caret"></span>
				  	  	</button>
				  	  	<ul class="dropdown-menu">
				  	  	  	<li><a href="#" onclick="removeSettings()">%settings-remove%</a></li>
				  	  	</ul>
				  	</div>
				</div>
				<div class="alert alert-danger" id="settings-notwritable">
			    %settings-security-notwritable-dir%
			    </div>
				<ul class="nav nav-tabs">
				    <li class="active"><a href="#home-tab" data-toggle="tab">%settings-primary%</a></li>
				    <li><a href="#outservers-tab" data-toggle="tab">%settings-outservers%</a></li>
				    <li><a href="#security-tab" data-toggle="tab">%settings-security%</a></li>
				    <li><a href="#diagnostics-tab" data-toggle="tab">%settings-diagnostics%</a></li>
			    </ul>
			    <div class="tab-content">
					<div class="tab-pane active fade in" id="home-tab">
						<table>
							<tr>
								<td>%threadsnum%</td>
								<td><input type="text" class="txtinput txtinput-inline form-control" id="THREADS" value="4"></td>
							</tr>
							<tr>
								<td>%timeoutlen%</td>
								<td><input type="text" class="txtinput txtinput-inline form-control" id="TIMEOUT" value="0">
									плавающая
									<input type="checkbox" id="randomTimeout" style="display:none;">
									<button type="button" class="btn btn-danger btn-checkbox" data-toggle="#randomTimeout" ><i class="glyphicon glyphicon-remove"></i></button>
								</td>
							</tr>
							<tr>
								<td>%settings-history-length%</td>
								<td><input type="text" class="txtinput txtinput-inline form-control" id="maxDoneSize" value="25"></td>
							</tr>
							<div class="alert alert-danger" id="send-in-background-notavalable">
						    %send-in-background-notavalable%
						    </div>
							<tr>
								<td>%settings-send-in-background%</td>
								<td><input type="checkbox" id="sendInBackground" style="display:none;">
								<button type="button" class="btn btn-danger btn-checkbox" data-toggle="#sendInBackground" <?php if(SERVICEMODE):?>disabled<?php endif;?>><i class="glyphicon glyphicon-remove"></i></button>
								<?php if(SERVICEMODE):?><span style="color:red;font-size:12px;">%servicemode-not-available%</span><?php endif; ?></td>
							</tr>
							<tr>
								<td>%timezone%</td>
								<td>
									<select id="timezone" class="form-control" >
										<?php
										$timezones=timezone_identifiers_list();
										foreach ($timezones as $zone):
										?>
										<option <?php echo $zone==$timezone?'selected':''?>><?php echo $zone;?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						</table>
						<table class="second">
							<tr>
								<td>%transfer-text-in-base64%</td>
								<td>
									<input type="checkbox" id="sendInBase64" style="display:none;">
									<button type="button" class="btn btn-danger btn-checkbox" data-toggle="#sendInBase64" ><i class="glyphicon glyphicon-remove"></i></button>
								</td>
							</tr>
							<tr>
								<td>%save-sended-to-txt-log%</td>
								<td>
									<input type="checkbox" id="saveLogInTxt" style="display:none;">
									<button type="button" class="btn btn-danger btn-checkbox" data-toggle="#saveLogInTxt" ><i class="glyphicon glyphicon-remove"></i></button>
								</td>
							</tr>
							<tr>
								<td>%insert-test-email-every%</td>
								<td>
									<input type="text" id="testEmailCounter" class="txtinput txtinput-inline form-control" value="0"> %insert-test-email-every2%
								</td>
							</tr>
							<tr>
								<td>%test-email%</td>
								<td>
									<input type="text" id="testEmail" class="txtinput txtinput-inline form-control" style="width:250px;" placeholder="your@email.com" readonly>
								</td>
							</tr>
						</table>
					</div>
					<div class="tab-pane fade" id="outservers-tab">
					<div class="alert alert-danger" id="shells-notavalable">
				    %outservers-not-available%
				    </div>
					<?php if(SERVICEMODE):?><span style="color:red;">%servicemode-not-available%</span><br><?php endif; ?>
						<input type="checkbox" id="use_out_servers" style="display:none;">
						<button type="button" class="btn btn-danger btn-checkbox" data-toggle="#use_out_servers" <?php if(SERVICEMODE):?>disabled<?php endif;?>><i class="glyphicon glyphicon-remove"></i></button> %useoutservers%<br><br>
						%settings-outservers-doc%
						<br>
						<textarea id="out_servers" class="form-control" style="width:100%; height:250px;" placeholder="<?php if(SERVICEMODE):?>Редактирование внешних серверов отключено в демонстрационном режиме<?php else: ?>Пример: http://serv4.ru/sw.php|c99|login:password<?php endif;?>"  <?php if(SERVICEMODE):?>readonly<?php endif;?>></textarea><br>
						<button class="btn btn-default" onclick="pingoutservers()" <?php if(SERVICEMODE):?>disabled<?php endif;?>>%settings-outservers-check%</button> %settings-outservers-check-autoremove%
						<br><br>
						<div class="progress" id="outprogressbar">
					    	<div class="bar bar-warning" style="width: 0%;"></div>
					    	<div class="barcounter">0/0</div>
					    </div>
					    %settings-outservers-check-log%
					    <div class="well" id="pingout_log" style="width:100%; height:150px;overflow-y:scroll;"></div>
					</div>
					<div class="tab-pane fade" id="security-tab">
					<?php if(SERVICEMODE):?><span style="color:red;">%servicemode-not-available%</span><br><?php endif; ?>
						<?php 
						$no_write_perm=false;
						clearstatcache();
						if(!is_writable(__FILE__)) $no_write_perm=true;
						?>
					    <div class="alert alert-danger" id="settings-security-notwritable">
					    %settings-security-notwritable%
					    </div>
						<div class="alert alert-success" style="display:none;" id="passchangesuccess">
					    %settings-security-password-changed%
					    </div>
					    <div class="alert alert-danger" style="display:none;" id="passchangeerror">
					    %settings-security-password-not-changed%
					    </div>
				        <table border=0>
					        <tr>
						        <td><input type="text" id="inputLogin" class="txtinput form-control" placeholder="%login%" <?php if($no_write_perm || SERVICEMODE):?>readonly<?php endif;?> ></td>
						        <td><input type="text" id="inputPassword" class="txtinput form-control" placeholder="%password%" <?php if($no_write_perm || SERVICEMODE):?>readonly<?php endif;?>></td>
					        </tr>
					        <tr>
						        <td>
						        	<button type="button" class="btn btn-default" onclick="ChangePass($('#inputLogin').val(),$('#inputPassword').val())" <?php if($no_write_perm || SERVICEMODE):?>disabled<?php endif;?>>%settings-security-set-password%</button>
							    </td><td>
							        <button type="button" class="btn btn-danger" onclick="ChangePass('','');" <?php if($no_write_perm || SERVICEMODE):?>disabled<?php endif;?>>%settings-security-remove-password%</button>
						        </td>
					        </tr>
				        </table>
						<br><br>
						<input type="checkbox" id="use_proxy_server" style="display:none;" <?php if(SERVICEMODE):?>readonly<?php endif;?>>
						<button type="button" class="btn btn-danger btn-checkbox" data-toggle="#use_proxy_server" <?php if(SERVICEMODE):?>disabled<?php endif;?>><i class="glyphicon glyphicon-remove"></i></button> %settings-security-use-proxy%<br>
						<textarea class="form-control" id="proxy_server_uri" style="width:100%; height:250px;" placeholder="http://proxyserver.ru:8080" <?php if(SERVICEMODE):?>readonly<?php endif;?>></textarea>
					</div>
					<div class="tab-pane fade" id="diagnostics-tab">
						<table border="0" class="table">
							<tr class="file_is_writable">
								<td>%file_is_writable%</td>
								<td>?</td>
								<td>%file_is_writable_e%</td>
							</tr>
							<tr class="dir_is_writable">
								<td>%dir_is_writable%</td>
								<td>?</td>
								<td>%dir_is_writable_e%</td>
							</tr>
							<tr class="settings_is_writable">
								<td>%settings_is_writable%</td>
								<td>?</td>
								<td>%settings_is_writable_e%</td>
							</tr>
							<tr class="bgfiles_is_writable">
								<td>%bgfiles_is_writable%</td>
								<td>?</td>
								<td>%bgfiles_is_writable_e%</td>
							</tr>
							<tr class="shells_available">
								<td>%shells_available%</td>
								<td>?</td>
								<td>%shells_available_e%</td>
							</tr>
							<tr class="allow_url_fopen">
								<td>%allow_url_fopen%</td>
								<td>?</td>
								<td>%allow_url_fopen_e%</td>
							</tr>
							<tr class="post_max_size">
								<td>%post_max_size%</td>
								<td>?</td>
								<td>%post_max_size_e%</td>
							</tr>
							<tr class="upload_max_filesize">
								<td>%upload_max_filesize%</td>
								<td>?</td>
								<td>%upload_max_filesize_e%</td>
							</tr>
						</table>
						<button class="btn btn-default" onclick="selfDiagnostics()">%refresh%</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row section-screen" id="help-screen">
		<div class="col-md-12">
			<div class="well">
				%helppage%
			</div>
		</div>
	</div>
	
	<div class="row section-screen" id="preview-screen">
		<div class="col-md-12">
			<div class="well">
				<h3>%preview%</h3>
				<button class="btn prime-button">%backtoeditor%</button>
				<iframe src="about:blank" style="width:100%; height:600px;"></iframe>
				<button class="btn prime-button">%backtoeditor%</button>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="well" style="text-align:center;">
			%name% <?php echo FULLVERSION;?> - &copy; <a href="http://a-l-e-x-u-s.ru/" target="_blank">Alexus Lab</a> <?php echo date("Y");?>
		</div>
	</di>
</div>
<?php if(SERVICEMODE) echo $metrikaCounter; ?>
</body>
</html>
<?php $translation->End(); ?>