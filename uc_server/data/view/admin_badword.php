<?php if(!defined('UC_ROOT')) exit('Access Denied');?>
<?php include $this->gettpl('header');?>

<script src="js/common.js" type="text/javascript"></script>
<script type="text/javascript">
	function switchbtn(btn) {
		$('srchuserdiv').style.display = btn == 'srch' ? '' : 'none';
		$('srchuserdiv').className = btn == 'srch' ? 'tabcontentcur' : '' ;
		$('srchuserbtn').className = btn == 'srch' ? 'tabcurrent' : '';
		$('adduserdiv').style.display = btn == 'srch' ? 'none' : '';
		$('adduserdiv').className = btn == 'srch' ? '' : 'tabcontentcur';
		$('adduserbtn').className = btn == 'srch' ? '' : 'tabcurrent';
		$('tmenu').style.height = btn == 'srch' ? '80'+'px' : '280'+'px';
	}
</script>
<div class="container">
	<?php if($status) { ?>
		<div class="correctmsg"><p><?php if($status == 2) { ?>������˳ɹ����¡�<?php } elseif($status == 1) { ?>���������ӳɹ���<?php } ?></p></div>
	<?php } ?>
	<div id="tmenu" class="hastabmenu">
		<ul class="tabmenu">
			<li id="srchuserbtn" class="tabcurrent"><a href="#" onclick="switchbtn('srch');">��Ӵ������</a></li>
			<li id="adduserbtn"><a href="#" onclick="switchbtn('add');">�������</a></li>
		</ul>
		<div id="adduserdiv" class="tabcontent" style="display:none;">
			<form action="admin.php?m=badword&a=ls" method="post">
				<ul class="tiplist">
					<li>ÿ��һ�飬����������滻����֮��ʹ�á�=�����зָ</li>
					<li>����뽫ĳ������ֱ���滻�� **��ֻ������Ｔ�ɡ�</li>
					<li><strong>���磺</strong></li>
					<li>toobad</li>
					<li>badword=good</li>
				</ul>
				<textarea name="badwords" class="bigarea"></textarea>
				<ul class="optlist">
					<li><input type="radio" name="type" value="2" id="badwordsopt2" class="radio" checked="checked" /><label for="badwordsopt2">����ͻʱ������ԭ���Ĵʱ�</label></li>
					<li><input type="radio" name="type" value="1" id="badwordsopt1" class="radio" /><label for="badwordsopt1">����ͻʱ������ԭ���Ĵʱ�</label></li>
					<li><input type="radio" name="type" value="0" id="badwordsopt0" class="radio" /><label for="badwordsopt0">��յ�ǰ�ʱ������´���˲������ɻָ�����������<a href="admin.php?m=badword&a=export" target="_blanks">�����ʱ�</a>�����ñ��ݣ�</label></li>
				</ul>
				<input type="submit" name="multisubmit" value="�� ��" class="btn" />
			</form>

		</div>
		<div id="srchuserdiv" class="tabcontentcur">
			<form action="admin.php?m=badword&a=ls" method="post">
			<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
			<table>
				<tr>
					<td>��������:</td>
					<td><input type="text" name="findnew" class="txt" /></td>
					<td>�滻Ϊ:</td>
					<td><input type="text" name="replacementnew" class="txt" /></td>
					<td><input type="submit" value="�� ��"  class="btn" /></td>
				</tr>
			</table>
			</form>
		</div>
	</div>
	<br />
	<h3>�������</h3>
	<div class="mainbox">
		<?php if($badwordlist) { ?>
			<form action="admin.php?m=badword&a=ls" method="post">
				<table class="datalist fixwidth">
					<tr>
						<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall">ɾ��</label></th>
						<th style="text-align:right;padding-right:11px;">��������</th>
						<th></th>
						<th>�滻Ϊ</th>
						<th>������</th>
					</tr>
					<?php foreach((array)$badwordlist as $badword) {?>
						<tr>
							<td class="option"><input type="checkbox" name="delete[]" value="<?php echo $badword['id'];?>" class="checkbox" /></td>
							<td class="tdinput"><input type="text" name="find[<?php echo $badword['id'];?>]" value="<?php echo $badword['find'];?>" title="����༭���ύ�󱣴�" class="txtnobd" onblur="this.className='txtnobd'" onfocus="this.className='txt'" /></td>
							<td class="tdarrow">&gt;</td>
							<td class="tdinput"><input type="text" name="replacement[<?php echo $badword['id'];?>]" value="<?php echo $badword['replacement'];?>" title="����༭���ύ�󱣴�" class="txtnobd"  onblur="this.className='txtnobd'" onfocus="this.className='txt'" style="text-align:left;" /></td>
							<td><?php echo $badword['admin'];?></td>
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

<?php include $this->gettpl('footer');?>