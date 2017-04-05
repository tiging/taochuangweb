<?php if(!defined('UC_ROOT')) exit('Access Denied');?>
<?php include $this->gettpl('header');?>

<script src="js/common.js" type="text/javascript"></script>
<script type="text/javascript">
var apps = new Array();
var run = 0;
function testlink() {
	if(apps[run]) {
		$('status_' + apps[run]).innerHTML = '��������...';
		$('link_' + apps[run]).src = $('link_' + apps[run]).getAttribute('testlink') + '&sid=<?php echo $sid;?>';
	}
	run++;
}
window.onload = testlink;
</script>
<div class="container">
	<?php if($a == 'ls') { ?>
		<h3 class="marginbot">Ӧ���б�<a href="admin.php?m=app&a=add" class="sgbtn">�����Ӧ��</a></h3>
		<?php if(!$status) { ?>
			<div class="note fixwidthdec">
				<p class="i">������֡�ͨ��ʧ�ܡ����������༭����������Ӧ��������Ӧ�� IP��</p>
			</div>
		<?php } elseif($status == '2') { ?>
			<div class="correctmsg"><p>Ӧ���б�ɹ����¡�</p></div>
		<?php } ?>
		<div class="mainbox">
			<?php if($applist) { ?>
				<form action="admin.php?m=app&a=ls" method="post">
					<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
					<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
						<tr>
							<th nowrap="nowrap"><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall">ɾ��</label></th>
							<th nowrap="nowrap">ID</th>
							<th nowrap="nowrap">Ӧ������</th>
							<th nowrap="nowrap">Ӧ�õ��� URL</th>
							<th nowrap="nowrap">ͨ�����</th>
							<th nowrap="nowrap">����</th>
						</tr>
						<?php $i = 0;?>
						<?php foreach((array)$applist as $app) {?>
							<tr>
								<td width="50"><input type="checkbox" name="delete[]" value="<?php echo $app['appid'];?>" class="checkbox" /></td>
								<td width="35"><?php echo $app['appid'];?></td>
								<td><a href="admin.php?m=app&a=detail&appid=<?php echo $app['appid'];?>"><strong><?php echo $app['name'];?></strong></a></td>
								<td><a href="<?php echo $app['url'];?>" target="_blank"><?php echo $app['url'];?></a></td>
								<td width="90"><div id="status_<?php echo $app['appid'];?>"></div><script id="link_<?php echo $app['appid'];?>" testlink="admin.php?m=app&a=ping&inajax=1&url=<?php echo urlencode($app['url']);?>&ip=<?php echo urlencode($app['ip']);?>&appid=<?php echo $app['appid'];?>&random=<?php echo rand()?>"></script><script>apps[<?php echo $i;?>] = '<?php echo $app['appid'];?>';</script></td>
								<td width="40"><a href="admin.php?m=app&a=detail&appid=<?php echo $app['appid'];?>">�༭</a></td>
							</tr>
							<?php $i++?>
						<?php } ?>
						<tr class="nobg">
							<td colspan="9"><input type="submit" value="�� ��" class="btn" /></td>
						</tr>
					</table>
					<div class="margintop"></div>
				</form>
			<?php } else { ?>
				<div class="note">
					<p class="i">Ŀǰû����ؼ�¼!</p>
				</div>
			<?php } ?>
		</div>
	<?php } elseif($a == 'add') { ?>
		<h3 class="marginbot">�����Ӧ��<a href="admin.php?m=app&a=ls" class="sgbtn">����Ӧ���б�</a></h3>
		<div class="mainbox">
			<table class="opt">
				<tr>
					<th>ѡ��װ��ʽ:</th>
				</tr>
				<tr>
					<td>
						<input type="radio" name="installtype" class="radio" checked="checked" onclick="$('url').style.display='none';$('custom').style.display='';" />�Զ��尲װ
						<input type="radio" name="installtype" class="radio" onclick="$('url').style.display='';$('custom').style.display='none';" />URL ��װ (�Ƽ�)
					</td>
				</tr>
			</table>
			<div id="url" style="display:none;">
				<form method="post" action="" target="_blank" onsubmit="document.appform.action=document.appform.appurl.value;" name="appform">
					<table class="opt">
						<tr>
							<th>Ӧ�ó���װ��ַ:</th>
						</tr>
						<tr>
							<td><input type="text" name="appurl" size="50" value="http://domainname/install/index.php" style="width:300px;" /></td>
						</tr>
					</table>
					<div class="opt">
						<input type="hidden" name="ucapi" value="<?php echo UC_API;?>" />
						<input type="hidden" name="ucfounderpw" value="<?php echo $md5ucfounderpw;?>" />
						<input type="submit" name="installsubmit"  value=" �� װ " class="btn" />
					</div>
				</form>
			</div>
			<div id="custom">
				<form action="admin.php?m=app&a=add" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
					<table class="opt">
						<tr>
							<th colspan="2">Ӧ������:</th>
						</tr>
						<tr>
							<td>
							<select name="type">
								<?php foreach((array)$typelist as $typeid => $typename) {?>
									<option value="<?php echo $typeid;?>"> <?php echo $typename;?> </option>
								<?php }?>
							</select>
							</td>
							<td></td>
						</tr>
						<tr>
							<th colspan="2">Ӧ������:</th>
						</tr>
						<tr>
							<td><input type="text" class="txt" name="name" value="" /></td>
							<td>�� 20 �ֽڡ�</td>
						</tr>
						<tr>
							<th colspan="2">Ӧ�õ��� URL:</th>
						</tr>
						<tr>
							<td><input type="text" class="txt" name="url" value="" /></td>
							<td>��Ӧ���� UCenter ͨ�ŵĽӿ� URL����β�벻Ҫ�ӡ�/�� ��Ӧ�õ�ֻ֪ͨ���͸��� URL</td>
						</tr>
						<tr>
							<th colspan="2">Ӧ�� IP:</th>
						</tr>
						<tr>
							<td><input type="text" class="txt" name="ip" value="" /></td>
							<td>������������ռ��ɡ�������������������⵼�� UCenter ���Ӧ��ͨ��ʧ�ܣ��볢������Ϊ��Ӧ�����ڷ������� IP ��ַ��</td>
						</tr>
						<tr>
							<th colspan="2">ͨ����Կ:</th>
						</tr>
						<tr>
							<td><input type="text" class="txt" name="authkey" value="" /></td>
							<td>ֻ����ʹ��Ӣ����ĸ�����֣��� 64 �ֽڡ�Ӧ�ö˵�ͨ����Կ����������ñ���һ�£������Ӧ�ý��޷��� UCenter ����ͨ�š�</td>
						</tr>


						<tr>
							<th colspan="2">Ӧ�õ�����·��:</th>
						</tr>
						<tr>
							<td>
								<input type="text" class="txt" name="apppath" value="" />
							</td>
							<td>Ĭ�������գ������д��Ϊ���·���������UC����������Զ�ת��Ϊ����·������ ../</td>
						</tr>
						<tr>
							<th colspan="2">�鿴��������ҳ���ַ:</th>
						</tr>
						<tr>
							<td>
								<input type="text" class="txt" name="viewprourl" value="" />
							</td>
							<td>URL����������Ĳ��֣��磺/space.php?uid=%s ����� %s ����uid</td>
						</tr>
						<tr>
							<th colspan="2">Ӧ�ýӿ��ļ�����:</th>
						</tr>
						<tr>
							<td>
								<input type="text" class="txt" name="apifilename" value="uc.php" />
							</td>
							<td>Ӧ�ýӿ��ļ����ƣ�����·����Ĭ��Ϊuc.php</td>
						</tr>
						<tr>
							<th colspan="2">��ǩ������ʾģ��:</th>
						</tr>
						<tr>
							<td><textarea class="area" name="tagtemplates"></textarea></td>
							<td valign="top">��ǰӦ�õı�ǩ������ʾ������Ӧ��ʱ�ĵ�������ģ�塣</td>
						</tr>

						<tr>
							<th colspan="2">��ǩģ����˵��:</th>
						</tr>
						<tr>
							<td><textarea class="area" name="tagfields"><?php echo $tagtemplates['fields'];?></textarea></td>
							<td valign="top">һ��һ�����˵����Ŀ���ö��ŷָ��Ǻ�˵�����֡��磺<br />subject,�������<br />url,�����ַ</td>
						</tr>
						<tr>
							<th colspan="2">�Ƿ���ͬ����¼:</th>
						</tr>
						<tr>
							<td>
								<input type="radio" class="radio" id="yes" name="synlogin" value="1" /><label for="yes">��</label>
								<input type="radio" class="radio" id="no" name="synlogin" value="0" checked="checked" /><label for="no">��</label>
							</td>
							<td>����ͬ����¼�󣬵��û��ڵ�¼����Ӧ��ʱ��ͬʱҲ���¼��Ӧ�á�</td>
						</tr>
						<tr>
							<th colspan="2">�Ƿ����֪ͨ:</th>
						</tr>
						<tr>
							<td>
								<input type="radio" class="radio" id="yes" name="recvnote" value="1"/><label for="yes">��</label>
								<input type="radio" class="radio" id="no" name="recvnote" value="0" checked="checked" /><label for="no">��</label>
							</td>
							<td></td>
						</tr>
					</table>
					<div class="opt"><input type="submit" name="submit" value=" �� �� " class="btn" tabindex="3" /></div>
				</form>
			</div>
		</div>
	<?php } else { ?>
		<h3 class="marginbot">�༭Ӧ��<a href="admin.php?m=app&a=ls" class="sgbtn">����Ӧ���б�</a></h3>
		<?php if($updated) { ?>
			<div class="correctmsg"><p>���³ɹ���</p></div>
		<?php } elseif($addapp) { ?>
			<div class="correctmsg"><p>�ɹ����Ӧ�á�</p></div>
		<?php } ?>
		<div class="mainbox">
			<form action="admin.php?m=app&a=detail&appid=<?php echo $appid;?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="opt">
					<tr>
						<th colspan="2">ID: <?php echo $appid;?></th>
					</tr>
					<tr>
						<th colspan="2">Ӧ������:</th>
					</tr>
					<tr>
						<td>
						<select name="type">
							<?php foreach((array)$typelist as $typeid => $typename) {?>
							<option value="<?php echo $typeid;?>" <?php if($typeid == $type) { ?>selected="selected"<?php } ?>> <?php echo $typename;?> </option>
							<?php }?>
						</select>
						</td>
						<td></td>
					</tr>

					<tr>
						<th colspan="2">Ӧ������:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="name" value="<?php echo $name;?>" /></td>
						<td>�� 20 �ֽڡ�</td>
					</tr>
					<tr>
						<th colspan="2">Ӧ�õ��� URL:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="url" value="<?php echo $url;?>" /></td>
						<td>��Ӧ���� UCenter ͨ�ŵĽӿ� URL����β�벻Ҫ�ӡ�/�� ��Ӧ�õ�ֻ֪ͨ���͸��� URL</td>
					</tr>
					<tr>
						<th colspan="2">Ӧ�õ����� URL:</th>
					</tr>
					<tr>
						<td><textarea name="extraurl" class="area"><?php echo $extraurl;?></textarea></td>
						<td>��Ӧ�ÿ��Է��ʵ����� URL����β�벻Ҫ�ӡ�/�� ��ÿ��һ����ֻ����ͬ����¼������� URL</td>
					</tr>
					<tr>
						<th colspan="2">Ӧ�� IP:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="ip" value="<?php echo $ip;?>" /></td>
						<td>������������ռ��ɡ�������������������⵼�� UCenter ���Ӧ��ͨ��ʧ�ܣ��볢������Ϊ��Ӧ�����ڷ������� IP ��ַ��</td>
					</tr>
					<tr>
						<th colspan="2">ͨ����Կ:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="authkey" value="<?php echo $authkey;?>" /></td>
						<td>ֻ����ʹ��Ӣ����ĸ�����֣��� 64 �ֽڡ�Ӧ�ö˵�ͨ����Կ����������ñ���һ�£������Ӧ�ý��޷��� UCenter ����ͨ�š�</td>
					</tr>

					<tr>
						<th colspan="2">Ӧ�õ�����·��:</th>
					</tr>
					<tr>
						<td>
							<input type="text" class="txt" name="apppath" value="<?php echo $apppath;?>" />
						</td>
						<td>Ĭ�������գ������д��Ϊ���·���������UC����������Զ�ת��Ϊ����·������ ../</td>
					</tr>
					<tr>
						<th colspan="2">�鿴��������ҳ���ַ:</th>
					</tr>
					<tr>
						<td>
							<input type="text" class="txt" name="viewprourl" value="<?php echo $viewprourl;?>" />
						</td>
						<td>URL����������Ĳ��֣��磺/space.php?uid=%s ����� %s ����uid</td>
					</tr>
					<tr>
						<th colspan="2">Ӧ�ýӿ��ļ�����:</th>
					</tr>
					<tr>
						<td>
							<input type="text" class="txt" name="apifilename" value="<?php echo $apifilename;?>" />
						</td>
						<td>Ӧ�ýӿ��ļ����ƣ�����·����Ĭ��Ϊuc.php</td>
					</tr>

					<tr>
						<th colspan="2">��ǩ������ʾģ��:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="tagtemplates"><?php echo $tagtemplates['template'];?></textarea></td>
						<td valign="top">��ǰӦ�õı�ǩ������ʾ������Ӧ��ʱ�ĵ�������ģ�塣</td>
					</tr>
					<tr>
						<th colspan="2">��ǩģ����˵��:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="tagfields"><?php echo $tagtemplates['fields'];?></textarea></td>
						<td valign="top">һ��һ�����˵����Ŀ���ö��ŷָ��Ǻ�˵�����֡��磺<br />subject,�������<br />url,�����ַ</td>
					</tr>
					<tr>
						<th colspan="2">�Ƿ���ͬ����¼:</th>
					</tr>
					<tr>
						<td>
							<input type="radio" class="radio" id="yes" name="synlogin" value="1" <?php echo $synlogin[1];?> /><label for="yes">��</label>
							<input type="radio" class="radio" id="no" name="synlogin" value="0" <?php echo $synlogin[0];?> /><label for="no">��</label>
						</td>
						<td>����ͬ����¼�󣬵��û��ڵ�¼����Ӧ��ʱ��ͬʱҲ���¼��Ӧ�á�</td>
					</tr>
					<tr>
						<th colspan="2">�Ƿ����֪ͨ:</th>
					</tr>
					<tr>
						<td>
							<input type="radio" class="radio" id="yes" name="recvnote" value="1" <?php echo $recvnotechecked[1];?> /><label for="yes">��</label>
							<input type="radio" class="radio" id="no" name="recvnote" value="0" <?php echo $recvnotechecked[0];?> /><label for="no">��</label>
						</td>
						<td></td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" �� �� " class="btn" tabindex="3" /></div>
<?php if($isfounder) { ?>
				<table class="opt">
					<tr>
						<th colspan="2">Ӧ�õ� UCenter ������Ϣ:</th>
					</tr>
					<tr>
						<th>
<textarea class="area" onFocus="this.select()">
define('UC_CONNECT', 'mysql');
define('UC_DBHOST', '<?php echo UC_DBHOST;?>');
define('UC_DBUSER', '<?php echo UC_DBUSER;?>');
define('UC_DBPW', '<?php echo UC_DBPW;?>');
define('UC_DBNAME', '<?php echo UC_DBNAME;?>');
define('UC_DBCHARSET', '<?php echo UC_DBCHARSET;?>');
define('UC_DBTABLEPRE', '`<?php echo UC_DBNAME;?>`.<?php echo UC_DBTABLEPRE;?>');
define('UC_DBCONNECT', '0');
define('UC_KEY', '<?php echo $authkey;?>');
define('UC_API', '<?php echo UC_API;?>');
define('UC_CHARSET', '<?php echo UC_CHARSET;?>');
define('UC_IP', '');
define('UC_APPID', '<?php echo $appid;?>');
define('UC_PPP', '20');
</textarea>
						</th>
						<td>��Ӧ�õ� UCenter ������Ϣ��ʧʱ�ɸ������Ĵ��뵽Ӧ�õ������ļ���</td>
					</tr>
				</table>
<?php } ?>
			</form>
		</div>
	<?php } ?>
</div>

<?php include $this->gettpl('footer');?>