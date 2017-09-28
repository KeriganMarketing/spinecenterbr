<?php ini_set('display_errors', 'Off');
$page_base='/search/view/';

function boldTerm($string,$searchterm){
	$string = str_replace($searchterm,'<strong>'.$searchterm.'</strong>',$string);
	return $string;
}

/*function myTruncate($string, $limit, $break=".", $pad="...",$searchterm) { // return with no change if string is shorter than $limit
	$string = str_replace('>','> ',$string);
	$string = boldTerm($string,$searchterm);
	$string = strip_tags($string);
	if(strlen($string) <= $limit) return $string; // is $break present between $limit and the end of the string?  
	if(false !== ($breakpoint = strpos($string, $break, $limit))) { if($breakpoint < strlen($string) - 1) { $string = substr($string, 0, $breakpoint) . $pad; } }
	return $string;
}*/
// define 'MySQL' class
class MySQL{
	private $conId;
	private $host;
	private $user;
	private $password;
	private $database;
	private $result;
	const OPTIONS=4;
	
	public function __construct($options=array()){
			if(count($options)!=self::OPTIONS){
			throw new Exception('Invalid number of connection parameters');
		}
		foreach($options as $parameter=>$value){
			if(!$value){
				throw new Exception('Invalid parameter '.$parameter);
			}
			$this->{$parameter}=$value;
		}
		$this->connectDB();
	}
	// connect to MySQL
	private function connectDB(){
		if(!$this->conId=mysql_connect($this->host,$this->user,$this->password)){
			throw new Exception('Error connecting to the server');
		}
		if(!mysql_select_db($this->database,$this->conId)){
			throw new Exception('Error selecting database');
		}
	}
	// run query
	public function query($query){
		if(!$this->result=mysql_query($query,$this->conId)){
			throw new Exception('Error performing query '.$query.mysql_error());
		}
		return new Result($this,$this->result);
	}
	public function escapeString($value){
		return mysql_escape_string($value);
	}
}
// define 'Result' class\
class Result {
	private $mysql;
	private $result;
	public function __construct(&$mysql,$result){
		$this->mysql=&$mysql;
		$this->result=$result;
	}
	// fetch row
	public function fetchRow(){
		return mysql_fetch_assoc($this->result);
	}
	
	// count rows
	public function countRows(){
		if(!$rows=mysql_num_rows($this->result)){
			return false;
		}
		return $rows;
	}
	
	// count affected rows
	public function countAffectedRows(){
		if(!$rows=mysql_affected_rows($this->mysql->conId)){
			throw new Exception('Error counting affected rows');
		}
		return $rows;
	}
	
	// get ID form last-inserted row
	public function getInsertID(){
		if(!$id=mysql_insert_id($this->mysql->conId)){
			throw new Exception('Error getting ID');
		}
		return $id;
	}
	
	// seek row
	public function seekRow($row=0){
		if(!is_int($row)||$row<0){
			throw new Exception('Invalid result set offset');
		}
		if(!mysql_data_seek($this->result,$row)){
			throw new Exception('Error seeking data');
		
		}
	}
}
// connect to MySQL
$db=new MySQL(array('host'=>$host,'user'=>$db_user,'password'=>$db_pass,'database'=>$db));
$searchterm=$db->escapeString($_GET['query']);

//build inital query
$fields = "title, pageContent";
$query = "SELECT * FROM pageTable WHERE MATCH($fields) AGAINST ('$searchterm' IN BOOLEAN MODE) AND searchIndex='1'";
$result=$db->query($query);

// pagination calculations
$page = $db->escapeString($_GET['page']);
if($page == ''){ $page = 1; }
$show = 5;
$count = $result->countRows();
$ceil = ($count / $show);
$pageCount = ceil($ceil);

//do math to work out how to limit the query to get the correct results
$start = ($page - 1) * $show;
//add limit
$query .= " LIMIT $start, $show";

//rerun query
$result=$db->query($query);

$search = '
<div id="search-results">
	<form  method="get" id="search" action="'.$page_base.'">
		<input type="text" class="text" name="query" style="color:#939393;width:300px;" '; if($searchterm == ''){ $search .= ' onfocus="this.value=\'\'; this.style.color=\'\'" value=" search our site"'; }else{ $search .= ' value=" '.$searchterm.'"'; } $search .= ' /><input class="button blue" type="submit" value="Search" />  
	</form>
	<div class="clear"></div>
';
if(strlen($searchterm) < 4){
	$search .= '<h2>Your search term must be more at least 4 characters</h2>'."\n";
}elseif(!$result->countRows()){
	$search .= '<h2>No results were found. Please try a new search.</h2>'."\n";
}else{
	// display search results
	
	$search .= '<h2>Your search criteria returned '.$count.' result(s).</h2>'."\n";
	
	while($row=$result->fetchRow()){
		$url=$site_url;
		if($row['parent'] != ''){
			$url.=$row['parent'].'/';
		}
		$url.=$row['controller'].'/';
		
		$search .= '
		<div class="item">
			<h2><a href="'.$url.'">'.boldTerm($row['title'],$searchterm).'</a></h2>
			<p class="url"><a href="'.$url.'">'.boldTerm($url,$searchterm).'</a></p>
			<p class="search-content">'.boldTerm(myTruncate(strip_tags($row['pageContent']), 200, " ", ' <a href="'.$url.'">visit&nbsp;page...</a>',$searchterm),$searchterm).'</p><br>
		</div>
		';
	}
}
//Display pagination
$search .= '<div class="pagination"><p>';
if($page != 1){
	$prev = $page - 1;
	$search .= ' <a class="prev" href="'.$page_base.'?query='.$searchterm.'&page='.$prev.'">&lt;</a> ';
}

$i = 1;
while($i <= $pageCount && $pageCount != 1){
	if($i != $page){$search .= ' <a href="'.$page_base.'?query='.$searchterm.'&page='.$i.'">'; }else{ $search .= '<span>'; }
	$search .= $i;
	if($i != $page){$search .= '</a> '; }else{ $search .= '</span>'; }
	$i++;
}

if($page != $pageCount && $pageCount != 0){
	$next = $page + 1;
	$search .= ' <a class="next" href="'.$page_base.'?query='.$searchterm.'&page='.$next.'">&gt;</a> ';
}

$search .= '</p><div class="clear"></div></div><br><br>';
$search .= '</div>';

$body = $search;

include('support.php');
?>