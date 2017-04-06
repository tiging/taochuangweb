<?php if(!defined('UC_ROOT')) exit('Access Denied');?>
<?php include $this->gettpl('header');?>

<?php if($a == 'ls') { ?>

	<script src="js/common.js" type="text/javascript"></script>
	<script src="js/calendar.js" type="text/javascript"></script>
	<script type="text/javascript">
		function switchbtn(btn) {
			$('addadmindiv').className = btn == 'addadmin' ? 'tabcontentcur' : '' ;
			$('editpwdiv').className = btn == 'addadmin' ? '' : 'tabcontentcur';

			$('addadmin').className = btn == 'addadmin' ? 'tabcurrent' : '';
			$('editpw').className = btn == 'addadmin' ? '' : 'tabcurrent';

			$('addadmindiv').style.display = btn == 'addadmin' ? '' : 'none';
			$('editpwdiv').style.display = btn == 'addadmin' ? 'none' : '';
		}
		function chkeditpw(theform) {
			if(theform.oldpw.value == '') {
				alert('������ԭ��ʼ������');
				theform.oldpw.focus();
				return false;
			}
			if(theform.newpw.value == '') {
				alert('������������');
				theform.newpw.focus();
				return false;
			}
			if(theform.newpw2.value == '') {
				alert('���ظ�����������');
				theform.newpw2.focus();
				return false;
			}
			if(theform.newpw.value != theform.newpw2.value) {
				alert('������������벻һ��');
				theform.newpw2.focus();
				return false;
			}
			if(theform.newpw.value.length < 6 && !confirm('��������̫�̣����ܻ᲻��ȫ����ȷ���趨��������')) {
				theform.newpw.focus();
				return false;
			}
			return true;
		}
	</script>

	<div class="container">
		<?php if($status) { ?>
			<div class="<?php if($status > 0) { ?>correctmsg<?php } else { ?>errormsg<?php } ?>">
				<p>
				<?php if($status == 1) { ?> ��� <?php echo $addname;?> Ϊ����Ա�ɹ�
				<?php } elseif($status == -1) { ?> ��� <?php echo $addname;?> Ϊ����Ա�ɹ�
				<?php } elseif($status == -2) { ?> ��� <?php echo $addname;?> Ϊ����Աʧ��
				<?php } elseif($status == -3) { ?>�޴��û�: <?php echo $addname;?>
				<?php } elseif($status == -4) { ?> /data/config.inc.php �ļ�����д
				<?php } elseif($status == -5) { ?> ��ʼ���˺������������
				<?php } elseif($status == -6) { ?> ������������벻һ��
				<?php } elseif($status == 2) { ?> ��ʼ���˺������޸ĳɹ�
				<?php } ?>
				</p>
			</div>
		<?php } ?>
		<div class="hastabmenu" style="height:218px;">
			<ul class="tabmenu">
				<li id="addadmin" class="tabcurrent"><a href="#" onclick="switchbtn('addadmin');">���UCenter����Ա</a></li>
				<?php if($user['isfounder']) { ?><li id="editpw"><a href="#" onclick="switchbtn('editpw');">�޸�UCenter��ʼ������</a></li><?php } ?>
			</ul>
			<div id="addadmindiv" class="tabcontentcur">
				<form action="admin.php?m=admin&a=ls" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="dbtb">
					<tr>
						<td class="tbtitle">�û���:</td>
						<td><input type="text" name="addname" class="txt" /></td>
					</tr>
					<tr>
						<td valign="top" class="tbtitle">Ȩ����:</td>
						<td>
							<ul class="dblist">
								<li><input type="checkbox" name="allowadminsetting" value="1" class="checkbox" checked="checked" />����ı�����</li>
								<li><input type="checkbox" name="allowadminapp" value="1" class="checkbox" />�������Ӧ��</li>
								<li><input type="checkbox" name="allowadminuser" value="1" class="checkbox" />��������û�</li>
								<li><input type="checkbox" name="allowadminbadword" value="1" class="checkbox" checked="checked" />�������������</li>
								<li><input type="checkbox" name="allowadmintag" value="1" class="checkbox" checked="checked" />�������TAG</li>
								<li><input type="checkbox" name="allowadminpm" value="1" class="checkbox" checked="checked" />����������Ϣ</li>
								<li><input type="checkbox" name="allowadmincredits" value="1" class="checkbox" checked="checked" />����������</li>
								<li><input type="checkbox" name="allowadmindomain" value="1" class="checkbox" checked="checked" />���������������</li>
								<li><input type="checkbox" name="allowadmindb" value="1" class="checkbox" />�����������</li>
								<li><input type="checkbox" name="allowadminnote" value="1" class="checkbox" checked="checked" />������������б�</li>
								<li><input type="checkbox" name="allowadmincache" value="1" class="checkbox" checked="checked" />���������</li>
								<li><input type="checkbox" name="allowadminlog" value="1" class="checkbox" checked="checked" />����鿴��־</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" name="addadmin" value="�� ��" class="btn" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			<?php if($user['isfounder']) { ?>
			<div id="editpwdiv" class="tabcontent" style="display:none;">
				<p class="i">�˴������ΪUCenter��̨��ʼ�˵�½���룬���Ӧ�ù���Ա�����޹ظ������Զ�ͬ�����ģ���������Ʊ��������</p>
				<form action="admin.php?m=admin&a=ls" onsubmit="return chkeditpw(this)" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="dbtb" style="height:123px;">
					<tr>
						<td class="tbtitle">������:</td>
						<td><input type="password" name="oldpw" class="txt" /></td>
					</tr>
					<tr>
						<td class="tbtitle">������:</td>
						<td><input type="password" name="newpw" class="txt" /></td>
					</tr>
					<tr>
						<td class="tbtitle">�ظ�������:</td>
						<td><input type="password" name="newpw2" class="txt" /></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" name="editpwsubmit" value="�� ��" class="btn" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			<?php } ?>
		</div>
		<h3>����Ա�б�</h3>
		<div class="mainbox">
			<?php if($userlist) { ?>
				<form action="admin.php?m=admin&a=ls" onsubmit="return confirm('��ȷ��ɾ����');" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
					<tr>
						<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" value="1" class="checkbox" /><label for="chkall">ɾ��</label></th>
						<th>�û���</th>
						<th>Email</th>
						<th>ע������</th>
						<th>ע��IP</th>
						<th>����</th>
						<th>Ȩ��</th>
					</tr>
					<?php foreach((array)$userlist as $user) {?>
						<tr>
							<td class="option"><input type="checkbox" name="delete[]" value="<?php echo $user['uid'];?>" value="1" class="checkbox" /></td>
							<td class="username"><?php echo $user['username'];?></td>
							<td><?php echo $user['email'];?></td>
							<td class="date"><?php echo $user['regdate'];?></td>
							<td class="ip"><?php echo $user['regip'];?></td>
							<td class="ip"><a href="admin.php?m=user&a=edit&uid=<?php echo $user['uid'];?>&fromadmin=yes">����</a></td>
							<td class="ip"><a href="admin.php?m=admin&a=edit&uid=<?php echo $user['uid'];?>">Ȩ��</a></td>
						</tr>
					<?php } ?>
					<tr class="nobg">
						<td><input type="submit" value="�� ��" class="btn" /></td>
						<td class="tdpage" colspan="4"><?php echo $multipage;?></td>
					</tr>
				</table>
				</form>
			<?php } else { ?>
				<div class="note">
					<p class="i">Ŀǰû����ؼ�¼!</p>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php if($_POST['editpwsubmit']) { ?>
		<script type="text/javascript">
		switchbtn('editpw');
		</script>
	<?php } else { ?>
		<script type="text/javascript">
		switchbtn('addadmin');
		</script>
	<?php } ?>

<?php } else { ?>
	<div class="container">
		<h3 class="marginbot">�༭����ԱȨ��<a href="admin.php?m=admin&a=ls" class="sgbtn">���ع���Ա�б�</a></h3>
		<?php if($status == 1) { ?>
			<div class="correctmsg"><p>�༭����ԱȨ�޳ɹ�</p></div>
		<?php } elseif($status == -1) { ?>
			<div class="correctmsg"><p>�༭����ԱȨ��ʧ��</p></div>
		<?php } else { ?>
			<div class="note">��������š�����Ӧ�á����������û��������������ݡ�Ȩ��</div>
		<?php } ?>
		<div class="mainbox">
			<form action="admin.php?m=admin&a=edit&uid=<?php echo $uid;?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="opt">
					<tr>
						<th>����Ա <?php echo $admin['username'];?>:</th>
					</tr>
					<tr>
						<td>
							<ul>
								<li><input type="checkbox" name="allowadminsetting" value="1" class="checkbox" <?php if($admin['allowadminsetting']) { ?> checked="checked" <?php } ?>/>����ı�����</li>
								<li><input type="checkbox" name="allowadminapp" value="1" class="checkbox" <?php if($admin['allowadminapp']) { ?> checked="checked" <?php } ?>/>�������Ӧ��</li>
								<li><input type="checkbox" name="allowadminuser" value="1" class="checkbox" <?php if($admin['allowadminuser']) { ?> checked="checked" <?php } ?>/>��������û�</li>
								<li><input type="checkbox" name="allowadminbadword" value="1" class="checkbox" <?php if($admin['allowadminbadword']) { ?> checked="checked" <?php } ?>/>�������������</li>
								<li><input type="checkbox" name="allowadmintag" value="1" class="checkbox" <?php if($admin['allowadmintag']) { ?> checked="checked" <?php } ?>/>�������TAG</li>
								<li><input type="checkbox" name="allowadminpm" value="1" class="checkbox" <?php if($admin['allowadminpm']) { ?> checked="checked" <?php } ?>/>����������Ϣ</li>
								<li><input type="checkbox" name="allowadmincredits" value="1" class="checkbox" <?php if($admin['allowadmincredits']) { ?> checked="checked" <?php } ?>/>����������</li>
								<li><input type="checkbox" name="allowadmindomain" value="1" class="checkbox" <?php if($admin['allowadmindomain']) { ?> checked="checked" <?php } ?>/>���������������</li>
								<li><input type="checkbox" name="allowadmindb" value="1" class="checkbox" <?php if($admin['allowadmindb']) { ?> checked="checked" <?php } ?>/>�����������</li>
								<li><input type="checkbox" name="allowadminnote" value="1" class="checkbox" <?php if($admin['allowadminnote']) { ?> checked="checked" <?php } ?>/>������������б�</li>
								<li><input type="checkbox" name="allowadmincache" value="1" class="checkbox" <?php if($admin['allowadmincache']) { ?> checked="checked" <?php } ?>/>���������</li>
								<li><input type="checkbox" name="allowadminlog" value="1" class="checkbox" <?php if($admin['allowadminlog']) { ?> checked="checked" <?php } ?>/>����鿴��־</li>
							</ul>
						</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" �� �� " class="btn" tabindex="3" /></div>
			</form>
		</div>
	</div>

<?php } ?>

<?php include $this->gettpl('footer');?>