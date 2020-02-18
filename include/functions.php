<?php
	function connect()
	{
		$connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME)
			or die('Не удалось подключиться к БД!<br>Error: ' . mysqli_connect_error());
		return $connection;
	}

	function close($connection)
	{
		mysqli_close($connection);
	}

	function exist($var)
	{
		if(isset($var) and $var!=="") return TRUE;
			else return FALSE;
	}

	function get_cat()
	{
		if (exist(trim($_GET['cat']))) {
			$cat = trim($_GET['cat']);
		} else $cat = FALSE;
		return $cat;
	}

	function site_name()
	{
		$site = substr($_SERVER['REQUEST_URI'], 1);
		$a = explode("?", $site);
		$site = $a[0];
		return $site;
	}

	function get_date($timestamp)
	{
		$time = getdate($timestamp);
		$time["minutes"] = date("i", $timestamp);
		$months = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
		$out = $time["mday"]." ".$months[$time["mon"]-1]." ".$time["year"]." ".$time["hours"].":".$time["minutes"];
		return $out;
	}

	function map_img($link)
	{
		$token = substr($link, 26, 16);
		$out = "https://static.coggle.it/diagram/".$token."/thumbnail?cachebust=3";
		return $out;
	}

	function enter_crush()
	{
		setcookie('user_id', 0, time()-100, "/");
		setcookie('user_hash', 0, time()-100, "/");
		header('Location: /');
		exit;
	}

	function userdata($conn)
	{
		$user_data = [];
		if(exist(trim($_COOKIE['user_id'])) and exist(trim($_COOKIE['user_hash']))) {
			$query = mysqli_query($conn, "SELECT * FROM `users` WHERE `id` = ".trim($_COOKIE['user_id']));
			if ($query) {
				if (mysqli_num_rows($query) == 0) {
					enter_crush();
				} else $user_data = mysqli_fetch_assoc($query);
				if (trim($_COOKIE['user_hash']) !== $user_data['hash']) {
					enter_crush();
				} else return $user_data;
			} else enter_crush();
		} return $user_data;
	}

	function get_page()
	{
		$cat = get_cat();
		$site = site_name();
		$search = trim($_GET['search']);
		if (exist(trim($_GET['page']))) {
			if (trim($_GET['page']) == 1) {
				$header = "Location: /".$site;
				if ($cat !== FALSE) {
					$header .= "?cat=".$cat;
				}
				if ($search !== "") {
					if ($cat !== FALSE) {
						$header .= "&";
					} else $header .= "?";
					$header .= "search=".$search;
				}
				header($header);
			}
			$page = trim($_GET['page']);
			$page = (int) $page;
		} else $page = 1;
		return $page;
	}

	function get_posts_id_for_cat($conn)
	{
		$cat = get_cat();
		$posts_id_array = [];
		if ($cat !== FALSE) {
			$posts_id_for_cat_query = mysqli_query($conn, "SELECT `id` FROM `maps` WHERE `category_id`=".$cat);
			while ($result = mysqli_fetch_assoc($posts_id_for_cat_query)) {
				$posts_id_array[] = $result['id'];
			}
			if ($posts_id_array !== []) {
				$posts_id_for_cat = "`id`=";
				$posts_id_for_cat .= join(" OR `id` = ", $posts_id_array);
				return $posts_id_for_cat;
			} else return FALSE;
		}
	}

	function select($conn, $offset, $search, $checked = 1, $nocat = FALSE, $nooffset = FALSE)
	{
		$page = get_page();
		$cat = get_cat();
		if ($nocat) {
			$cat = FALSE;
		}
		$sql = "SELECT * FROM `maps` WHERE";
		if (exist($search)) {
			$sql .= " `name` LIKE '%$search%' AND ";
		}
		$sql .= " (`checked` = ".$checked." AND `published` = ".$checked.")";
		if ($cat !== FALSE) {
			$posts_id_for_cat = get_posts_id_for_cat($conn);
			if ($posts_id_for_cat !== FALSE) {
				$sql .= " AND ({$posts_id_for_cat}) ORDER BY `time` DESC LIMIT $offset";
			} else $sql .= "LIMIT 0";
		} else $sql .= " ORDER BY `time` DESC LIMIT $offset";
		if (!$nooffset) {
			$sql .= " OFFSET ".$offset*($page - 1);
		}
		$query = mysqli_query($conn, $sql);
		return $query;
	}

	function select_cat($conn, $search, $checked = 1)
	{
		$select = select($conn, maps_count($conn, $checked), $search, $checked, TRUE, TRUE);
		$sql = "SELECT * FROM `categories` WHERE 1";
		$cat_id = [];
		while ($result = mysqli_fetch_assoc($select)) {
			$cat_id[] = $result['category_id'];
		}
		$sql .= " AND (0";
		foreach ($cat_id as $value) {
			$sql .= " OR `id` = $value";
		}
		$sql .= ") ";
		if ($checked == TRUE) {
			$sql .= " AND `checked` = 1";
		}
		$query = mysqli_query($conn, $sql);
		return $query;
	}

	function select_notices($conn, $user_id)
	{
		$sql = "SELECT * FROM `maps` WHERE (`checked` = 1 AND `published` = 0 AND `author_id` = $user_id) ORDER BY `id` DESC";
		$query = mysqli_query($conn, $sql);
		return $query;
	}

	function pagination_count($conn, $offset, $checked = 1)
	{
		$search = trim($_GET['search']);
		$select_all = select($conn, maps_count($conn, $checked), $search, $checked, FALSE, TRUE);
		$count = ceil(mysqli_num_rows($select_all)/$offset);
		$count = (int) $count;
		return $count;
	}

	function select_all_cat($conn)
	{
		$query = mysqli_query($conn, "SELECT * FROM `categories` WHERE `checked` = 1");
		return $query;
	}

	function select_comments($conn, $map_id)
	{
		$select = mysqli_query($conn, "SELECT * FROM `comments` WHERE `map_id` = ".$map_id." ORDER BY `time` DESC");
		return $select;
	}

	function maps_count($conn, $checked=1)
	{
		$count_query = mysqli_query($conn, "SELECT COUNT(`id`) AS `count` FROM `maps` WHERE `checked` = $checked AND `published` = $checked");
		$result = mysqli_fetch_assoc($count_query);
		$count = $result['count'];
		return $count;
	}

	function get_article_assoc($conn, $id)
	{
		$select = mysqli_query($conn, "SELECT * FROM `maps` WHERE `id` = ".$id);
		$result = mysqli_fetch_assoc($select);
		return $result;
	}

	function get_cat_name($conn, $id)
	{
		$query = mysqli_query($conn, "SELECT * FROM `categories` WHERE `id`=".$id);
		if ($query !== FALSE) {
			$result = mysqli_fetch_assoc($query);
			$cat = $result['title'];
			return $cat;
		} else return FALSE;
	}

	function get_user_name($conn, $id)
	{
		$query = mysqli_query($conn, "SELECT * FROM `users` WHERE `id`=".$id);
		if ($query !== FALSE) {
			$result = mysqli_fetch_assoc($query);
			$name = $result['username'];
			return $name;
		} else return FALSE;
	}

	function get_description($line, $id, $length = 150)
	{
		$words = explode(" ", $line);
		$out = "";
		foreach ($words as $word) {
			if (mb_strlen($out.$word) <= $length) {
				$out .= " ".$word;
			} else {
				$out .= "<a href='/template.php?id=$id' class='post-sm-link'>Дальше...</a>";
				break;
			}
		} return $out;
	}

	function generate_hash($length = '32')
	{
		$symbol = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890!@#$%&";
		$code = "";
		for ($i = 0; $i < $length; $i++) { 
			$code .= $symbol[rand(0, strlen($symbol)-1)];
		}
		return $code;
	}

	function get_pagination($conn, $offset, $checked = 1)
	{
		$cat = get_cat();
		$count = pagination_count($conn, $offset, $checked);
		$site = site_name();
		$check = get_page();
		$search = trim($_GET['search']);
		if ($count == 1 or $count == 0) {
			return NULL;
		}
		$out = "";
		$out.="<div class='col-md-12'><div class='pagination'><ul class='pagination-list'>";
		if ($cat !== FALSE) {
			if ($check !== 1) {
				$out.="<li class='pagination-list-li'><a class='pagination-list-link' href='/{$site}?cat={$cat}";	
				if($search !== "") $out.= "&search={$search}";
				$out.="&page=".($check-1)."'>&lt;</a></li>";
			}  else $out.="<li class='pagination-list-li pagination-list-li_disable'>&lt;</li>";
			for ($i = 0; $i < $count; $i++) {
				if ($i == ($check-1)) {
					$out.="<li class='pagination-list-li pagination-list-li_active'>".($i+1)."</li>";
				}
				else {
					$out.="<li class='pagination-list-li'><a class='pagination-list-link' href='/{$site}?cat={$cat}";
					if($search !== "") $out.= "&search={$search}";
					$out.="&page=".($i+1)."'>".($i+1)."</a></li>";
				}
			}
			if ($check !== $count) {
				$out.="<li class='pagination-list-li'><a class='pagination-list-link' href='/{$site}?cat={$cat}";
				if($search !== "") $out.= "&search={$search}";
				$out.="&page=".($check+1)."'>&gt;</a></li>";
			}  else $out.="<li class='pagination-list-li pagination-list-li_disable'>&gt;</li>";
		} else {
			if ($check !== 1) {
				$out.="<li class='pagination-list-li'><a class='pagination-list-link' href='/{$site}?page=".($check-1);
				if($search !== "") $out.= "&search={$search}";
				$out.="'>&lt;</a></li>";
			} else $out.="<li class='pagination-list-li pagination-list-li_disable'>&lt;</li>";
			for ($i=0; $i < $count; $i++) {
				$check = get_page($site);
				if ($i == ($check-1)) {
					$out.="<li class='pagination-list-li pagination-list-li_active'>".($i+1)."</li>";
				}
				else {
					$out.="<li class='pagination-list-li'><a class='pagination-list-link' href='/{$site}?page=".($i+1);
					if($search !== "") $out.= "&search={$search}";
					$out.="'>".($i+1)."</a></li>";
				}
			}
			if ($check !== $count) {
				$out.="<li class='pagination-list-li'><a class='pagination-list-link' href='/{$site}?page=".($check+1);
				if($search !== "") $out.= "&search={$search}";
				$out.="'>&gt;</a></li>";
			}  else $out.="<li class='pagination-list-li pagination-list-li_disable'>&gt;</li>";
		}
		$out.="</ul></div></div>";
		return $out;
	}

	function account_exit()
	{
		if (exist($_POST['exit'])) {
			setcookie('user_id', 0, time()-100, "/");
			setcookie('user_hash', 0, time()-100, "/");
			header("Refresh: 0");
		}
	}

	function admin_check($conn)
	{
		$userdata = userdata($conn);
		if ($userdata['admin'] == 1) {
			return TRUE;
		}
		else return FALSE;
	}

	function good_cookie($cookie)
	{
		if ($_COOKIE[$cookie] == TRUE) {
			setcookie($cookie, FALSE, time()-30, "/");
			return TRUE;
		} else return NULL;
	}
?>