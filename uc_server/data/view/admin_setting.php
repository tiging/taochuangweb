<?php if(!defined('UC_ROOT')) exit('Access Denied');?>
<?php include $this->gettpl('header');?>

<script src="js/common.js" type="text/javascript"></script>

<div class="container">
	<?php if($updated) { ?>
		<div class="correctmsg"><p>���³ɹ���</p></div>
	<?php } elseif($a == 'register') { ?>
		<div class="note fixwidthdec"><p class="i">����/��ֹ�� Email ��ַֻ����д Email ���������֣�ÿ��һ������������ @hotmail.com</p></div>
	<?php } ?>
	<?php if($a == 'ls') { ?>
		<div class="mainbox nomargin">
			<form action="admin.php?m=setting&a=ls" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="opt">
					<tr>
						<th colspan="2">���ڸ�ʽ:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="dateformat" value="<?php echo $dateformat;?>" /></td>
						<td>ʹ�� yyyy(yy) ��ʾ�꣬mm ��ʾ�£�dd ��ʾ�졣�� yyyy-mm-dd ��ʾ 2000-1-1</td>
					</tr>
					<tr>
						<th colspan="2">ʱ���ʽ:</th>
					</tr>
					<td>
						<input type="radio" id="hr24" class="radio" name="timeformat" value="1" <?php echo $timeformat[1];?> /><label for="hr24">24 Сʱ��</label>
						<input type="radio" id="hr12" class="radio" name="timeformat" value="0" <?php echo $timeformat[0];?> /><label for="hr12">12 Сʱ��</label>
					</td>
					</tr>
					<tr>
						<th colspan="2">ʱ��:</th>
					</tr>
					<tr>
						<td>
							<select name="timeoffset">
								<option value="-12" <?php echo $checkarray['012'];?>>(GMT -12:00) Eniwetok, Kwajalein</option>
								<option value="-11" <?php echo $checkarray['011'];?>>(GMT -11:00) Midway Island, Samoa</option>
								<option value="-10" <?php echo $checkarray['010'];?>>(GMT -10:00) Hawaii</option>
								<option value="-9" <?php echo $checkarray['09'];?>>(GMT -09:00) Alaska</option>
								<option value="-8" <?php echo $checkarray['08'];?>>(GMT -08:00) Pacific Time (US &amp; Canada), Tijuana</option>
								<option value="-7" <?php echo $checkarray['07'];?>>(GMT -07:00) Mountain Time (US &amp; Canada), Arizona</option>
								<option value="-6" <?php echo $checkarray['06'];?>>(GMT -06:00) Central Time (US &amp; Canada), Mexico City</option>
								<option value="-5" <?php echo $checkarray['05'];?>>(GMT -05:00) Eastern Time (US &amp; Canada), Bogota, Lima, Quito</option>
								<option value="-4" <?php echo $checkarray['04'];?>>(GMT -04:00) Atlantic Time (Canada), Caracas, La Paz</option>
								<option value="-3.5" <?php echo $checkarray['03.5'];?>>(GMT -03:30) Newfoundland</option>
								<option value="-3" <?php echo $checkarray['03'];?>>(GMT -03:00) Brassila, Buenos Aires, Georgetown, Falkland Is</option>
								<option value="-2" <?php echo $checkarray['02'];?>>(GMT -02:00) Mid-Atlantic, Ascension Is., St. Helena</option>
								<option value="-1" <?php echo $checkarray['01'];?>>(GMT -01:00) Azores, Cape Verde Islands</option>
								<option value="0" <?php echo $checkarray['0'];?>>(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia</option>
								<option value="1" <?php echo $checkarray['1'];?>>(GMT +01:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome</option>
								<option value="2" <?php echo $checkarray['2'];?>>(GMT +02:00) Cairo, Helsinki, Kaliningrad, South Africa</option>
								<option value="3" <?php echo $checkarray['3'];?>>(GMT +03:00) Baghdad, Riyadh, Moscow, Nairobi</option>
								<option value="3.5" <?php echo $checkarray['3.5'];?>>(GMT +03:30) Tehran</option>
								<option value="4" <?php echo $checkarray['4'];?>>(GMT +04:00) Abu Dhabi, Baku, Muscat, Tbilisi</option>
								<option value="4.5" <?php echo $checkarray['4.5'];?>>(GMT +04:30) Kabul</option>
								<option value="5" <?php echo $checkarray['5'];?>>(GMT +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
								<option value="5.5" <?php echo $checkarray['5.5'];?>>(GMT +05:30) Bombay, Calcutta, Madras, New Delhi</option>
								<option value="5.75" <?php echo $checkarray['5.75'];?>>(GMT +05:45) Katmandu</option>
								<option value="6" <?php echo $checkarray['6'];?>>(GMT +06:00) Almaty, Colombo, Dhaka, Novosibirsk</option>
								<option value="6.5" <?php echo $checkarray['6.5'];?>>(GMT +06:30) Rangoon</option>
								<option value="7" <?php echo $checkarray['7'];?>>(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
								<option value="8" <?php echo $checkarray['8'];?>>(GMT +08:00) &#x5317;&#x4eac;(Beijing), Hong Kong, Perth, Singapore, Taipei</option>
								<option value="9" <?php echo $checkarray['9'];?>>(GMT +09:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>
								<option value="9.5" <?php echo $checkarray['9.5'];?>>(GMT +09:30) Adelaide, Darwin</option>
								<option value="10" <?php echo $checkarray['10'];?>>(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok</option>
								<option value="11" <?php echo $checkarray['11'];?>>(GMT +11:00) Magadan, New Caledonia, Solomon Islands</option>
								<option value="12" <?php echo $checkarray['12'];?>>(GMT +12:00) Auckland, Wellington, Fiji, Marshall Island</option>
							</select>
						</td>
						<td>Ĭ��Ϊ: GMT +08:00</td>
					</tr>
					<!--
					<tr>
						<th colspan="2">�����û���¼ʧ�ܴ���:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="login_failedtime" value="<?php echo $login_failedtime;?>" /></td>
						<td>�û���¼ʧ�ܳ������õ����ݣ�����15�������޷���¼��0Ϊ�����ƴ���</td>
					</tr>
					-->
					<tr>
						<th colspan="2">������Ϣ����ע������:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="pmsendregdays" value="<?php echo $pmsendregdays;?>" /></td>
						<td>ע���������ڴ����õģ��������Ͷ���Ϣ��0Ϊ�����ƣ��˾�Ϊ�����ƻ����˷����</td>
					</tr>
					<tr>
						<th colspan="2">ͬһ�û��� 24 Сʱ�����������˻Ự�������:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="privatepmthreadlimit" value="<?php echo $privatepmthreadlimit;?>" /></td>
						<td>ͬһ�û��� 24 Сʱ�ڿ��Է�������˻Ự�������ֵ�������� 30 - 100 ��Χ��ȡֵ��0 Ϊ�����ƣ��˾�Ϊ������ͨ���������������</td>
					</tr>
					<tr>
						<th colspan="2">ͬһ�û��� 24 Сʱ��������Ⱥ�ĻỰ�������:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="chatpmthreadlimit" value="<?php echo $chatpmthreadlimit;?>" /></td>
						<td>ͬһ�û��� 24 Сʱ�ڿ��Է����Ⱥ�ĻỰ�����ֵ�������� 30 - 100 ��Χ��ȡֵ��0 Ϊ�����ƣ��˾�Ϊ������ͨ���������������</td>
					</tr>
					<tr>
						<th colspan="2">����ͬһȺ�ĻỰ������û���:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="chatpmmemberlimit" value="<?php echo $chatpmmemberlimit;?>" /></td>
						<td>ͬһ�Ự������ж����û��������ã������� 30 - 100 ��Χ��ȡֵ��0Ϊ������</td>
					</tr>
					<tr>
						<th colspan="2">������Ϣ��ˮԤ��:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="pmfloodctrl" value="<?php echo $pmfloodctrl;?>" /></td>
						<td>���η�����Ϣ���С�ڴ�ʱ�䣬��λ�룬0 Ϊ�����ƣ��˾�Ϊ������ͨ���������������</td>
					</tr>

					<tr>
						<th colspan="2">���ö���Ϣ����:</th>
					</tr>
					<tr>
					<td>
						<input type="radio" id="pmcenteryes" class="radio" name="pmcenter" value="1" <?php echo $pmcenter[1];?> onclick="$('hidden1').style.display=''"  /><label for="pmcenteryes">��</label>
						<input type="radio" id="pmcenterno" class="radio" name="pmcenter" value="0" <?php echo $pmcenter[0];?> onclick="$('hidden1').style.display='none'" /><label for="pmcenterno">��</label>
					</td>
					<td>�Ƿ����ö���Ϣ���Ĺ��ܣ���Ӱ��ʹ�ö���Ϣ�ӿ�Ӧ�ó����ʹ��</td>
					</tr>
					<tbody id="hidden1" <?php echo $pmcenter['display'];?>>
					<tr>
						<th colspan="2">�������Ͷ���Ϣ��֤��:</th>
					</tr>
					<tr>
						<td>
							<input type="radio" id="sendpmseccodeyes" class="radio" name="sendpmseccode" value="1" <?php echo $sendpmseccode[1];?> /><label for="sendpmseccodeyes">��</label>
							<input type="radio" id="sendpmseccodeno" class="radio" name="sendpmseccode" value="0" <?php echo $sendpmseccode[0];?> /><label for="sendpmseccodeno">��</label>
						</td>
						<td>�Ƿ�������Ϣ���ķ��Ͷ���Ϣ��֤�룬���Է�ֹʹ�û����񷢶���Ϣ</td>
					</tr>
					</tbody>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" �� �� " class="btn" tabindex="3" /></div>
			</form>
		</div>
	<?php } elseif($a == 'register') { ?>
		<div class="mainbox nomargin">
			<form action="admin.php?m=setting&a=register" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="opt">
					<tr>
						<th colspan="2">�Ƿ�����ͬһ Email ��ַע�����û�:</th>
					</tr>
					<tr>
						<td>
							<input type="radio" id="yes" class="radio" name="doublee" value="1" <?php echo $doublee[1];?> /><label for="yes">��</label>
							<input type="radio" id="no" class="radio" name="doublee" value="0" <?php echo $doublee[0];?> /><label for="no">��</label>
						</td>
					</tr>
					<tr>
						<th colspan="2">����� Email ��ַ:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="accessemail"><?php echo $accessemail;?></textarea></td>
						<td valign="top">ֻ����ʹ����Щ������β�� Email ��ַע�ᡣ</td>
					</tr>
					<tr>
						<th colspan="2">��ֹ�� Email ��ַ:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="censoremail"><?php echo $censoremail;?></textarea></td>
						<td valign="top">��ֹʹ����Щ������β�� Email ��ַע�ᡣ</td>
					</tr>
					<tr>
						<th colspan="2">��ֹ���û���:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="censorusername"><?php echo $censorusername;?></textarea></td>
						<td valign="top">��������ͨ�����ÿ���ؼ���һ�У���ʹ��ͨ��� "*" �� "*����*"(��������)��</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" �� �� " class="btn" tabindex="3" /></div>
			</form>
		</div>
	<?php } else { ?>
		<div class="mainbox nomargin">
			<form action="admin.php?m=setting&a=mail" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="opt">
					<tr>
						<th colspan="2">�ʼ���Դ��ַ:</th>
					</tr>
					<tr>
						<td><input name="maildefault" value="<?php echo $maildefault;?>" type="text"></td>
						<td>�������ʼ���ָ���ʼ���Դʱ��Ĭ��ʹ�ô˵�ַ��Ϊ�ʼ���Դ</td>
					<tr>
						<th colspan="2">�ʼ����ͷ�ʽ:</th>
					</tr>
					<tr>
						<td colspan="2">
							<label><input class="radio" name="mailsend" value="1"<?php if($mailsend == 1) { ?> checked="checked"<?php } ?> onclick="$('hidden1').style.display = 'none';$('hidden2').style.display = 'none';" type="radio"> ͨ�� PHP ������ sendmail ����(�Ƽ��˷�ʽ)</label><br />
							<label><input class="radio" name="mailsend" value="2"<?php if($mailsend == 2) { ?> checked="checked"<?php } ?> onclick="$('hidden1').style.display = '';$('hidden2').style.display = '';" type="radio"> ͨ�� SOCKET ���� SMTP ����������(֧�� ESMTP ��֤)</label><br />
							<label><input class="radio" name="mailsend" value="3"<?php if($mailsend == 3) { ?> checked="checked"<?php } ?> onclick="$('hidden1').style.display = '';$('hidden2').style.display = 'none';" type="radio"> ͨ�� PHP ���� SMTP ���� Email(�� Windows ��������Ч, ��֧�� ESMTP ��֤)</label>
						</td>
					</tr>
					<tbody id="hidden1"<?php if($mailsend == 1) { ?> style="display:none"<?php } ?>>
					<tr>
						<td colspan="2">SMTP ������:</td>
					</tr>
					<tr>
						<td>
							<input name="mailserver" value="<?php echo $mailserver;?>" class="txt" type="text">
						</td>
						<td valign="top">���� SMTP �������ĵ�ַ</td>
					</tr>
					<tr>
						<td colspan="2">SMTP �˿�:</td>
					</tr>
					<tr>
						<td>
							<input name="mailport" value="<?php echo $mailport;?>" type="text">
						</td>
						<td valign="top">���� SMTP �������Ķ˿ڣ�Ĭ��Ϊ 25</td>
					</tr>
					</tbody>
					<tbody id="hidden2"<?php if($mailsend == 1 || $mailsend == 3) { ?> style="display:none"<?php } ?>>
					<tr>
						<td colspan="2">SMTP ������Ҫ�������֤:</td>
					</tr>
					<tr>
						<td>
							<label><input type="radio" class="radio" name="mailauth"<?php if($mailauth == 1) { ?> checked="checked"<?php } ?> value="1" />��</label>
							<label><input type="radio" class="radio" name="mailauth"<?php if($mailauth == 0) { ?> checked="checked"<?php } ?> value="0" />��</label>
						</td>
						<td valign="top">��� SMTP ������Ҫ�������֤�ſ��Է��ţ���ѡ���ǡ�</td>
					</tr>
					<tr>
						<td colspan="2">�������ʼ���ַ:</td>
					</tr>
					<tr>
						<td>
							<input name="mailfrom" value="<?php echo $mailfrom;?>" class="txt" type="text">
						</td>
						<td valign="top">�����Ҫ��֤, ����Ϊ�����������ʼ���ַ���ʼ���ַ�����Ҫ�����û�������ʽΪ��username &lt;user@domain.com&gt;��</td>
					</tr>
					<tr>
						<td colspan="2">SMTP �����֤�û���:</td>
					</tr>
					<tr>
						<td>
							<input name="mailauth_username" value="<?php echo $mailauth_username;?>" type="text">
						</td>
						<td valign="top"></td>
					</tr>
					<tr>
						<td colspan="2">SMTP �����֤����:</td>
					</tr>
					<tr>
						<td>
							<input name="mailauth_password" value="<?php echo $mailauth_password;?>" type="text">
						</td>
						<td valign="top"></td>
					</tr>
					</tbody>
					<tr>
						<th colspan="2">�ʼ�ͷ�ķָ���:</th>
					</tr>
					<tr>
						<td>
							<label><input class="radio" name="maildelimiter"<?php if($maildelimiter == 1) { ?> checked="checked"<?php } ?> value="1" type="radio"> ʹ�� CRLF ��Ϊ�ָ���</label><br />
							<label><input class="radio" name="maildelimiter"<?php if($maildelimiter == 0) { ?> checked="checked"<?php } ?> value="0" type="radio"> ʹ�� LF ��Ϊ�ָ���</label><br />
							<label><input class="radio" name="maildelimiter"<?php if($maildelimiter == 2) { ?> checked="checked"<?php } ?> value="2" type="radio"> ʹ�� CR ��Ϊ�ָ���</label>
						</td>
						<td>
							��������ʼ������������õ����˲���
						</td>
					</tr>
					<tr>
						<th colspan="2">�ռ��˵�ַ�а����û���:</th>
					</tr>
					<tr>
						<td>
							<label><input type="radio" class="radio" name="mailusername"<?php if($mailusername == 1) { ?> checked="checked"<?php } ?> value="1" />��</label>
							<label><input type="radio" class="radio" name="mailusername"<?php if($mailusername == 0) { ?> checked="checked"<?php } ?> value="0" />��</label>
						</td>
						<td valign="top">ѡ���ǡ������ռ��˵��ʼ���ַ�а�����̳�û���</td>
					</tr>
					<tr>
						<th colspan="2">�����ʼ������е�ȫ��������ʾ:</th>
					</tr>
					<tr>
						<td>
							<label><input type="radio" class="radio" name="mailsilent"<?php if($mailsilent == 1) { ?> checked="checked"<?php } ?> value="1" />��</label>
							<label><input type="radio" class="radio" name="mailsilent"<?php if($mailsilent == 0) { ?> checked="checked"<?php } ?> value="0" />��</label>
						</td>
						<td valign="top">&nbsp;</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" �� �� " class="btn" tabindex="3" /></div>
			</form>
		</div>
	<?php } ?>
</div>

<?php include $this->gettpl('footer');?>