<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ajax-sql</title>
<style>
*{font-size: 12px;}
th,td{padding: 5px 10px;text-align: center;border: solid 1px #ddd;}
.edit{font-weight: normal;color: #390}
</style>
</head> 
<body>
<?php  
$url = dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);
?>
<a href="<?php echo $url; ?>">Home</a>
<a href="?action=insert_cat">Insert cat</a>
<a href="?action=insert_item">Insert item</a>
<?php if( !is_signin() ){ ?>
	<a href="?action=signout">Sign out</a>
<?php }else{ ?>
	<a href="?action=signin">Sign in</a>
<?php } ?>

<br>

<?php
date_default_timezone_set('PRC');

// user signin
$user_name = 'haozi';
$user_pass = 'hao^zi';

// connect sql
$nect = mysql_connect("localhost","root","123");
if (!$nect){
	die('数据库连接错误: ' . mysql_error());
}

// select db
mysql_select_db("link_db", $nect);

// get param
$param = '';
$cat = '';
$id = '';
if(is_array($_GET)&&count($_GET)>0){
    if(isset($_GET['action'])) {
    	$param = $_GET['action'];
    }
    if(isset($_GET['cat'])) {
    	$cat = $_GET['cat'];
    }
    if(isset($_GET['id'])) {
    	$id = $_GET['id'];
    }
}else{
	// select all cat
	$res01 = mysql_query("SELECT * FROM cats");
	if( $res01 ){
		while($row01 = mysql_fetch_array($res01)){
			$row01_id = $row01['ID'];
			if( is_signin() ) $edit_cat = '<a class="edit" href="?action=edit_cat&id='.$row01_id.'">Edit</a>'; else $edit_cat = '';
			echo '<h3><a href="?cat='.$row01_id.'">'.$row01['title'].'</a>'.$edit_cat.'</h3>';
			echo '<table><thead><tr><th>ID</th><th>Title</th><th>Info</th><th>URL</th><th>ICO</th><th>IMG</th><th>Rank</th><th>Cat</th><th>Time</th></tr></thead>';
			$res02 = mysql_query("SELECT * FROM links WHERE cat={$row01_id}");
			while($row = mysql_fetch_array($res02)){
				if( is_signin() ) $edit_item = '<td><a class="edit" href="?action=edit_item&id='.$row['ID'].'">Edit</a></td>'; else $edit_item = '';
				echo '<tr><td>'.$row['ID'].'</td>'.'<td>'.$row['title'].'</td>'.'<td>'.$row['info'].'</td>'.'<td>'.$row['url'].'</td>'.'<td>'.$row['ico'].'</td>'.'<td>'.$row['img'].'</td>'.'<td>'.$row['rank'].'</td>'.'<td>'.$row['cat'].'</td>'.'<td>'.$row['time'].'</td>'.$edit_item.'</tr>';
			}
			echo '</table>';
		}
	}else{
		header('Location: '.$url.'?action=init'); 
		exit;
	}
}

if( $cat ){
	$res01 = mysql_query("SELECT * FROM cats WHERE ID={$cat}");
	$tit = null;
	$row01_id = null;
	while($row01 = mysql_fetch_array($res01)){
		$tit = $row01['title'];
		$row01_id = $row01['ID'];
	}

	if( $tit ){
		if( is_signin() ) $edit_cat = '<a class="edit" href="?action=edit_cat&id='.$row01_id.'">Edit</a>'; else $edit_cat = '';
		echo '<h3>'.$tit.$edit_cat.'</h3><table><thead><tr><th>ID</th><th>Title</th><th>Info</th><th>URL</th><th>ICO</th><th>IMG</th><th>Rank</th><th>Cat</th><th>Time</th></tr></thead>';
		$res02 = mysql_query("SELECT * FROM links WHERE cat={$cat}");
		while($row = mysql_fetch_array($res02)){
			if( is_signin() ) $edit_item = '<td><a class="edit" href="?action=edit_item&id='.$row['ID'].'">Edit</a></td>'; else $edit_item = '';
			echo '<tr><td>'.$row['ID'].'</td>'.'<td>'.$row['title'].'</td>'.'<td>'.$row['info'].'</td>'.'<td>'.$row['url'].'</td>'.'<td>'.$row['ico'].'</td>'.'<td>'.$row['img'].'</td>'.'<td>'.$row['rank'].'</td>'.'<td>'.$row['cat'].'</td>'.'<td>'.$row['time'].'</td>'.$edit_item.'</tr>';		
		}
		echo '</table>';
	}else{
		header('Location: '.$url); 
		exit;
	}
}

// create tables
if( $param == 'init' ){
	if( is_signin() ){
		if (mysql_query("CREATE DATABASE link_db",$nect)){
			echo "数据库创建成功！";

			mysql_select_db("link_db", $nect);

			$a = "CREATE TABLE links (
				ID int(10) NOT NULL AUTO_INCREMENT,
				title varchar(120),
				info varchar(120),
				url varchar(120),
				ico varchar(120),
				img varchar(120),
				rank int(10) NOT NULL,
				cat int(10) NOT NULL,
				time datetime NOT NULL,
				UNIQUE KEY ID (ID)
			)";

			$b = "CREATE TABLE cats (
				ID int(10) NOT NULL AUTO_INCREMENT,
				title varchar(120),
				rank int(10) NOT NULL,
				time datetime NOT NULL,
				UNIQUE KEY ID (ID)
			)";

			mysql_query($a,$nect);
			mysql_query($b,$nect);
		}
	}else{
		signin();
	}
}

// insert item
elseif( $param == 'insert_item' ){

	if( is_signin() ){
		// get cat item
		$cat_item = null;
		$result2 = mysql_query("SELECT * FROM cats");
		while($row = mysql_fetch_array($result2)){
			$cat_item .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
		}
?>

		<form method="post" action="?action=insert_item_check">
			<ul>
				<li>Title: <input type="text" name="title"></li>
				<li>Info: <textarea name="info"></textarea></li>
				<li>Url: <input type="url" name="url"></li>
				<li>ICO: <input type="url" name="ico"></li>
				<li>Img: <input type="url" name="img"></li>
				<li>Rank: <select name="rank"><?php for ($i=0; $i < 5; $i++) echo '<option value="'.$i.'">'.$i.'</option>'; ?></select></li>
				<li>Cat: <select name="cat"><?php echo $cat_item; ?></select></li>
			</ul>
			<input type="submit" value="Insert data">
		</form>

<?php 
	}else{
		signin();
	}
}
// insert item check
elseif( $param == 'insert_item_check' ){  

	if( is_signin() ){
		$titles = null;
		$result2 = mysql_query("SELECT title FROM links");
		while($row = mysql_fetch_array($result2)){
			if( $row['title'] == $_REQUEST['title'] )
				$titles = $row['title'];
		}

		if( !$titles ){
			// insert
			mysql_query("INSERT INTO links (title, info, url, ico, img, rank, cat, time) VALUES ('".$_REQUEST['title']."','".$_REQUEST['info']."','".$_REQUEST['url']."','".$_REQUEST['ico']."','".$_REQUEST['img']."','".$_REQUEST['rank']."','".$_REQUEST['cat']."','".date('Y-m-d G:i:s')."')");
			die($_REQUEST['title'].' 已成功插入数据库！');
		}else{
			echo $_REQUEST['title'].' 已存在！';
		}
	}else{
		signin();
	}
} 

// insert cat
elseif( $param == 'insert_cat' ){ 

	if( is_signin() ){
?>

		<form method="post" action="?action=insert_cat_check">
			<ul>
				<li>Title: <input type="text" name="title"></li>
				<li>Rank: <select name="rank"><?php for ($i=0; $i < 5; $i++) echo '<option value="'.$i.'">'.$i.'</option>'; ?></select></li>
			</ul>
			<input type="submit" value="Insert data">
		</form>

<?php 
	}else{
		signin();
	}
}


// insert cat check
elseif( $param == 'insert_cat_check' ){ 

	if( is_signin() ){ 
		$titles = null;
		$result2 = mysql_query("SELECT title FROM cats");
		while($row = mysql_fetch_array($result2)){
			if( $row['title'] == $_REQUEST['title'] )
				$titles = $row['title'];
		}

		if( !$titles ){
			// insert
			mysql_query("INSERT INTO cats (title, rank, time) VALUES ('".$_REQUEST['title']."','".$_REQUEST['rank']."','".date('Y-m-d G:i:s')."')");
			die($_REQUEST['title'].' 已成功插入数据库！');
		}else{
			echo $_REQUEST['title'].' 已存在！';
		}
	}else{
		signin();
	}
}  

// edit cat
elseif( $param == 'edit_cat' && $id ){ 
	if( is_signin() ){ 
		if( isset($_REQUEST['title']) ){
			mysql_query("UPDATE cats SET title='".$_REQUEST['title']."', rank='".$_REQUEST['rank']."' WHERE ID={$id}");
			header('Location: '.$url.'?action=edit_cat&state=success&id='.$id); 
			exit;
		}else{
			$res = mysql_query("SELECT * FROM cats WHERE ID={$id}");
			while($row = mysql_fetch_array($res)){
				echo '<form method="post" action="?action=edit_cat&id='.$id.'"><ul>';
				echo '<li>ID: '.$row['ID'].'</li>';
				echo '<li>Title: <input type="text" name="title" value="'.$row['title'].'"></li>';
				echo '<li>Rank: <input type="number" name="rank" value="'.$row['rank'].'"> 0,1,2,3,4</li>';
				echo '</ul><input type="submit" value="Edit cat"></form>';
			}
		}
		if( isset($_GET['state']) == 'success' ) echo 'Edit success !!';
	}else{
		signin();
	}
}

// edit item
elseif( $param == 'edit_item' && $id ){ 
	if( is_signin() ){ 
		if( isset($_REQUEST['title']) ){
			mysql_query("UPDATE links SET title='".$_REQUEST['title']."', info='".$_REQUEST['info']."', url='".$_REQUEST['url']."', img='".$_REQUEST['img']."', ico='".$_REQUEST['ico']."', cat='".$_REQUEST['cat']."', time='".date('Y-m-d G:i:s')."', rank='".$_REQUEST['rank']."' WHERE ID={$id}");
			header('Location: '.$url.'?action=edit_item&state=success&id='.$id); 
			exit;
		}else{
			$res = mysql_query("SELECT * FROM links WHERE ID={$id}");
			while($row = mysql_fetch_array($res)){
				echo '<form method="post" action="?action=edit_item&id='.$id.'"><ul>';
				echo '<li>ID: '.$row['ID'].'</li>';
				echo '<li>Title: <input type="text" name="title" value="'.$row['title'].'"></li>';
				echo '<li>Info: <input type="text" name="info" value="'.$row['info'].'"></li>';
				echo '<li>Url: <input type="url" name="url" value="'.$row['url'].'"></li>';
				echo '<li>Img: <input type="url" name="img" value="'.$row['img'].'"></li>';
				echo '<li>ICO: <input type="url" name="ico" value="'.$row['ico'].'"></li>';
				echo '<li>Cat: <input type="number" name="cat" value="'.$row['cat'].'"></li>';
				echo '<li>Rank: <input type="number" name="rank" value="'.$row['rank'].'"> 0,1,2,3,4</li>';
				echo '</ul><input type="submit" value="Edit item"></form>';
			}
		}
		if( isset($_GET['state']) == 'success' ) echo 'Edit success !!';
	}else{
		signin();
	}
}

// user signin check
elseif( $param == 'signin_check' ){ 
	if( isset($_REQUEST['name']) && isset($_REQUEST['pass']) ){
		if( $_REQUEST['name'] == $user_name && $_REQUEST['pass'] == $user_pass ){
			setcookie('user',$_REQUEST['name'],time()+7200);
			setcookie('pass',$_REQUEST['pass'],time()+7200);
			header('Location: '.$url); 
			exit;
		}else{
			echo 'Name or pass error !!';
		}
	}
}

// user signin
elseif( $param == 'signin' ){ 
	if( !is_signin() ){
		echo 'You need to sign in to continue !';
		echo '<form method="post" action="?action=signin_check">';
			echo '<ul>';
				echo '<li>Name: <input type="text" name="name" value=""></li>';
				echo '<li>Pass: <input type="password" name="pass" value=""></li>';
			echo '</ul>';
			echo '<input type="submit" value="Sign in">';
		echo '</form>';
	}
}

// user signout
elseif( $param == 'signout' ){ 
	setcookie('user','',time()-3600);
	setcookie('pass','',time()-3600);
	header('Location: '.$url); 
	exit;
}
?>

<?php  

function is_signin(){
	global $user_name, $user_pass;
	if( isset($_COOKIE['user']) == $user_name && isset($_COOKIE['pass']) == $user_pass )
		return true;
	else
		return false;
}

function signin(){
	header('Location: '.$url.'?action=signin'); 
	exit;
}

?>

<?php mysql_close($nect); ?>
</body>
</html>