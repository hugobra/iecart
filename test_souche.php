<?php

// démarrage de la session pour stockage des variables de confiuguration
session_start();
if (isset($_SESSION['error_reporting'])) {
	// double utilisation de error_reporting pour s'assurer que la valeur retournée est bien celle que l'on a positionné (autrement il renvoie l'ancienne valeur
	$e = error_reporting($_SESSION['error_reporting']);
	$e = error_reporting($_SESSION['error_reporting']);
}
if (!empty($_SESSION['log_errors'])) {
	ini_set('log_errors', 1);
}
if (!empty($_SESSION['display_errors'])) {
	ini_set('display_errors', 1);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Page de test de la souche</title>

<script language="JavaScript" type="text/javascript">
<!--
function showHelp(nomZone) {
	if(document.getElementById)
	document.getElementById(nomZone).style.visibility = "visible";
}

function leaveHelp(nomZone) {
	if(document.getElementById)
	document.getElementById(nomZone).style.visibility = "hidden";
}
//-->
</script>


<style type="text/css"><!--


.nav {
	float: left;
}

.enligne li {
  display: inline;
  list-style-type: none;
  padding: 1em;
  border: 1px solid #666666;
  margin: 10px;
} 

.help {
	display: inline;
	font: 60% Verdana, Helvetica, Arial, sans-serif;
	color: grey;
}

div.box {
    border: none;
    margin: 1em 1em 2em 1em;
    padding: 0;
	width: 95%;
	max-width: 95%
}

div.box h5 { 
    background: #003366;
    border: 1px solid #003366;
    border-style: solid solid none solid;
    color: White;
    padding: 0em 1em 0em 1em;
    text-transform: lowercase;
    display: inline;
    font-size: 1em;
    height: 1em;
}

div.box div.body {
    background: transparent;
    border-collapse: collapse;
    border: 1px solid #003366;
	padding: 1em;
}

.contentTabs {
    background: transparent;
    border-collapse: collapse;
    border-bottom: 1px solid #003366;
    padding-left: 1em;
    margin-top: 2em;
    white-space: nowrap;
}

.contentTabs a {
    background: #336699;
    border: 1px solid #003366;
    border-style: solid solid none solid;
    color: White;
    font-weight: normal;
    height: 1.2em;
    margin-right: 0.5em;
    padding: 0em 2em;
    text-transform: lowercase;
	text-decoration: none;
}

.contentTabs a:hover {
	text-decoration: none;
}

.contentTabs a:visited {
    /*background: #336699;*/
    border: 1px solid #003366;
    border-style: solid solid none solid;
    color: White;
    font-weight: normal;
    height: 1.2em;
    margin-right: 0.5em;
    padding: 0em 2em;
    text-transform: lowercase;
}

.contentTabs a.selected {
    background: #FF0000;
    border-bottom: #003366 1px solid;
	text-color: #FFFFFF;
    color: #FFFFFF;
    font-weight: normal;
}

.contentTabs a:hover {
    background-color: #003366;
    color: White;
}

body {
    font: 100% Verdana, Helvetica, Arial, sans-serif;
    background: White;
    color: Black;
    margin: 0;
    padding: 0;
}

fieldset {
	padding: 0.5em 1em 1em 1em;
}

pre {margin: 0px; font-family: monospace;}
a:link {color: #000099; text-decoration: none; background-color: #ffffff;}

table {border-collapse: collapse;}
.center {text-align: center;}
.center table { margin-left: auto; margin-right: auto; text-align: left;}
.center th { text-align: center !important; }
td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
h1 {font-size: 150%;}
h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccccff; font-weight: bold; color: #000000;}
.h {background-color: #9999cc; font-weight: bold; color: #000000;}
.v {background-color: #cccccc; color: #000000; font-size: 75%;}
i {color: #666666; background-color: #cccccc;}
img {float: right; border: 0px;}
hr {width: 90%; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}

.ok {background-color:lightgreen;}
.ko {background-color:red;}
//--></style>
</head>
<body>
<?php

$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
switch ($mode) {
case 'apache':
	$action = Action::factory('apache');
	break;
case 'php':
	$action = Action::factory('php');
	break;
case 'mysql':
	$action = Action::factory('mysql');
	break;
case 'oracle':
	$action = Action::factory('oracle');
	break;
case 'ldap':
	$action = Action::factory('ldap');
	break;
case 'conf':
	$action = Action::factory('conf');
	break;
case 'ext':
	$action = Action::factory('ext');
	break;
default:
	$action = Action::factory('error');
	break;
} // switch


$action->execute();
echo Templates::getHeader();
$action->display();
echo Templates::getFooter();

class Url{
	var $_host = array();
	var $_script = array();
	var $_query = array();

	function Url($url = null){
		if (!is_null($url)) {
			$arr = parse_url($url);
			parse_str($arr['query'], $tab);
			$this->_host = $arr['scheme'] . '://' .  $arr['host'];
			$this->_script = $arr['path'];
			$this->_query = $tab;
		} else {
			$this->_host = $_SERVER['HTTP_HOST'];
			$this->_script = $_SERVER['SCRIPT_NAME'];
			$this->_query = $_GET;
		}
	}

	function addQueryString($key, $value)
	{
		$this->_query[$key] = $value;
	}

	function removeQueryString($key)
	{
		unset($this->_query[$key]);
	}

	function getUrl()
	{
		foreach($this->_query as $key => $value){
			$query[] = "$key=$value";
		}
		$queryString = (count($query) == 0) ? '' : '?' . implode('&amp;', $query);
		return 'http://' . $this->_host . $this->_script . $queryString;
	}

	function &getInstance($url = 'empty')
	{
		static $instance = array();
		if (!isset($instance[$url])) {
			$url = ($url == 'empty') ? null : $url;
			$instance[$url] =& new Url($url);
		}
		return $instance[$url];
	}

}

function getDump($var)
{
	ob_start();
	var_dump($var);
	$ouput = ob_get_contents();
	ob_end_clean();
	return $ouput;
}

class Templates{

	function Templates(){}

	function getHeader()
	{
		$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
		$url = Url::getInstance();
		$url->_query = array();
		$url->addQueryString('mode', 'conf');
		$url->addQueryString('action', 'form');
		$urlConf = $url->getUrl();
		$cssConf = ($mode == 'conf') ? 'selected' : 'plain';

		$url = Url::getInstance();
		$url->_query = array();
		$url->addQueryString('mode', 'apache');
		$urlApache = $url->getUrl();
		$cssApache = ($mode == 'apache') ? 'selected' : 'plain';

		$url->addQueryString('mode', 'php');
		$urlPhp = $url->getUrl();
		$cssPHP = ($mode == 'php') ? 'selected' : 'plain';

		$url->addQueryString('mode', 'mysql');
		$url->addQueryString('action', 'form');
		$urlMysql = $url->getUrl();
		$cssMysql = ($mode == 'mysql') ? 'selected' : 'plain';

		$url->addQueryString('mode', 'oracle');
		$url->addQueryString('action', 'form');
		$urlOracle = $url->getUrl();
		$cssOracle = ($mode == 'oracle') ? 'selected' : 'plain';

		$url->addQueryString('mode', 'ldap');
		$url->addQueryString('action', 'form');
		$urlLdap = $url->getUrl();
		$cssLdap = ($mode == 'ldap') ? 'selected' : 'plain';

		$url->addQueryString('mode', 'ext');
		$url->addQueryString('action', 'form');
		$urlExt = $url->getUrl();
		$cssExt = ($mode == 'ext') ? 'selected' : 'plain';

$html = <<<HTML
<div class="box">
	<div class="contentTabs">
		<a class="$cssConf" href="$urlConf">Configuration</a>
		<a class="$cssApache" href="$urlApache">Apache</a>
		<a class="$cssPHP" href="$urlPhp">Php</a>
		<a class="$cssMysql" href="$urlMysql">Mysql</a>
		<a class="$cssOracle" href="$urlOracle">Oracle</a>
		<a class="$cssLdap" href="$urlLdap">Ldap</a>
HTML;

	if (phpversion() >= "4.4.2") {
		$html .= <<<HTML
		<a class="$cssExt" href="$urlExt">Extensions</a>
HTML;
	}

	$html .= <<<HTML
</div>
	<div class="body">
			&nbsp;

HTML;
	return $html;
}

function getFooter()
{
	return <<<HTML
	</div>
</div>
HTML;
}

}

class Action{

	var $_html = '';

	function getHtml(){
		return $this->_html;
	}

	function setHtml($newValue){
		$this->_html = $newValue;
	}

	function factory($mode = null)
	{
		if (!is_null($mode)) {
			$class = ucfirst($mode) . 'Action';
			if (class_exists($class) && $class != 'ErrorAction') {
				return new $class();
			}
			return new ErrorAction('Veuillez sélectionner un mode de test ci-dessus');
		}
		return new ErrorAction('Aucun mode spécifié');
	}

	function getPhpInfo()
	{
		ob_start();
		phpinfo();
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function display()
	{
		echo $this->fetch();
	}

	function fetch()
	{
		return $this->getHtml();
	}

	function execute()
	{
		// ABSTRACT METHOD
	}

}

class ApacheAction extends Action{

	function ApacheAction(){}

	function execute()
	{
		$html = $this->getHtml();

		$html1 = $this->getPhpInfo();

		$html .= '<div align="center">';
		$apache1 = preg_match('/(<style type="text\/css">.*<\/style>)/s', $html1, $matches);

		$apache1 = preg_match('/(<h2><a name="module_apache2handler">apache2handler<\/a><\/h2>.*?<\/table>)/s', $html1, $matches);
		$html .= $matches[1];
		$apache1 = preg_match('/(<h2>Apache Environment<\/h2>.*?<\/table>)/s', $html1, $matches);
		$html .= $matches[1];
		$html .= '</div>';
		$this->setHtml($html);
	}

}

class PhpAction extends Action{

	function PhpAction(){}

	function showResume()
	{
		$html = $this->getHtml();

		$html1 = $this->getPhpInfo();

		$apache1 = preg_match('/(<style type="text\/css">.*<\/style>)/s', $html1, $matches);

		$html .= '<div class="center">';
		$apache1 = preg_match('/<body><div class="center">(.*?<\/table><br \/>.*?<\/table>.*?<\/table>)/s', $html1, $matches);
		$html .= $matches[1];
		$html .= '</div>';

		$this->setHtml($html);
	}

	function showAll()
	{
		$html = $this->getHtml();

		$htmlPhpInfo = $this->getPhpInfo();
		//preg_match('/(<style type="text\/css">.*<\/style>)/s', $htmlPhpInfo, $matches);
		preg_match('/<body.*?>(.*)<\/body>/s', $htmlPhpInfo, $matches);
		/*$htmlPhpInfo = preg_replace('/<body.*?>(.*)<\/body>/s', '', $htmlPhpInfo);*/

		$html .= $matches[1];
		$this->setHtml($html);
	}

	function showNav($default = null)
	{

		$url = Url::getInstance();
		$url->_query = array();
		$url->addQueryString('mode', 'php');
		$url->addQueryString('action', 'resume');
		$urlResume = $url->getUrl();
		$url->addQueryString('mode', 'php');
		$url->addQueryString('action', 'all');
		$urlAll = $url->getUrl();


		$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : null;
		if (is_null($action)) {
			$action  = (!is_null($default)) ? $default : null;
		}

		$html = $this->getHtml();
		switch($action){
			case 'resume':
				$itemResume = "Résumé";
				$itemAll = "<a href=\"$urlAll\">PHPInfo</a>";
				$this->showResume();
				break;
			case 'all':
				$itemAll = "PHPInfo";
				$itemResume = "<a href=\"$urlResume\">Résumé</a>";
				$this->showAll();
				break;
			default:
				$itemResume = "<a href=\"$urlResume\">Résumé</a>";
				$itemAll = "<a href=\"$urlAll\">PHPInfo</a>";
		} // switch

		$html .= <<<NAV

	

		<ul >
			<li>
				$itemResume
			</li>
			<li>
				$itemAll
			</li>
		</ul>			
	
	
NAV;

		$this->setHtml($html);
	}

	function execute()
	{
		$default = 'resume';

		$this->showNav($default);
		$_REQUEST['action'] = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : null;
		switch($_REQUEST['action']){
			case 'resume':
				$this->showResume();
				break;
			case 'all':
				$this->showAll();
				break;
			default:
				$func = 'show' . ucfirst($default);
				$this->$func();
		} // switch
	}
}

class MysqlAction extends Action{

	function MysqlAction(){

	}

	function execute()
	{
		switch($_REQUEST['action']){
			case 'form':
				$this->showForm();
				break;
			case 'connect':
				$this->showForm();
				$this->connect();
				break;
			default:

		} // switch
	}

	function connect()
	{
		extract($_POST);
		$html = $this->getHtml();
		
		// connexion avec mysqli
		if (!isset($mysql)) {
			ini_set('mysqli.default_socket', $sock);
			
			if (isset($useSock)) {
				$host = null;
				$port = null;
			} else {
				$sock = null;
			}
			if ($link = mysqli_connect($host, $user, $password, null, $port, $sock)) {
				$html .= "Connexion avec pilote mysqli réussie<br />\n";
				if (!empty($db)) {
					if (mysqli_select_db($link, $db)) {
						$html .= "Base de données <b>$db</b> sélectionnée<br />\n";
						if (!empty($table)) {
							$result = mysqli_query($link, "select * from $table");
							$html .= "<fieldset><legend>Listage de la table <b>$table</b></legend>\n";
							while($row = mysqli_fetch_array($result)) {
								$html .= implode(' | ', $row) . "<br />\n";
							}
							$html .= "</fieldset>";
							mysqli_free_result($result);
						} else {
							$html .= "aucune table spécifiée pour lecture<br />";
						}

					} else {
						$html .= "impossible de connecter à la base de données<br />";
					}
				}
				mysqli_close($link);
			} else{
				$html .= "Connexion avec pilote mysqli impossible";
			}
		} else {	// connexion avec mysql
			ini_set('mysql.default_socket', $sock);

			if (isset($useSock)) {
				$hostc = "localhost:$sock";
			} else {
				$hostc = "$host:$port";
			}
			if ($link = mysql_connect($hostc, $user, $password)) {
				$html .= "Connexion avec pilote mysql réussie<br />\n";
				if (!empty($db)) {
					if (mysql_select_db($db)) {
						$html .= "Base de données <b>$db</b> sélectionnée<br />\n";
						if (!empty($table)) {
							$result = mysql_query("select * from $table");
							$html .= "<fieldset><legend>Listage de la table <b>$table</b></legend>\n";
							while($row = mysql_fetch_array($result)) {
								$html .= implode(' | ', $row) . "<br />\n";
							}
							$html .= "</fieldset>";
							mysql_free_result($result);
						} else {
							$html .= "aucune table spécifiée pour lecture<br />";
						}

					} else {
						$html .= "impossible de connecter à la base de données<br />";
					}
				}
				mysql_close($link);
			} else{
				$html .= "Connexion avec pilote mysql impossible";
			}
		}
		$this->setHtml($html);
	}

	function showForm()
	{
		$url = Url::getInstance();
		$url->addQueryString('mode', 'mysql');
		$url->addQueryString('action', 'connect');

		$mds = get_cfg_var('mysql.default_socket');
		$mids = get_cfg_var('mysqli.default_socket');
		
		// on récupère les autres paramètres possibles
		$chkSock = isset($_POST['useSock']) ? 'checked' : '';
		$sock = isset($_POST['sock']) ? $_POST['sock'] : $mids;
		$host = isset($_POST['host']) ? $_POST['host'] : 'localhost';
		$port = isset($_POST['port']) ? $_POST['port'] : '3306';
		$user = isset($_POST['user']) ? $_POST['user'] : 'root';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$db = isset($_POST['db']) ? $_POST['db'] : '';
		$table = isset($_POST['table']) ? $_POST['table'] : '';
		
		$urlStr = $url->getUrl();
		$html = $this->getHtml();
		$html .= <<<FORM
<script language="JavaScript" type="text/javascript">
<!--
// masque les éléments inutiles
function majmode() {
	if (document.getElementById('useSock').checked == true) {
		document.getElementById('labsock').style.display='inline';
		document.getElementById('labhost').style.display='none';
		document.getElementById('labport').style.visibility='hidden';
	} else {
		document.getElementById('labsock').style.display='none';
		document.getElementById('labhost').style.display='inline';
		document.getElementById('labport').style.visibility='visible';
	}
}
// rechargement du socket par defaut
function reloadmds() {
	if (document.getElementById('mysql').checked == true) {
		if (document.getElementById('sock').value != document.getElementById('mds').value && confirm('Remettre le socket à "mysql.default_socket" ?')) {
			document.getElementById('sock').value = document.getElementById('mds').value;
		}
	} else {
		if (document.getElementById('sock').value != document.getElementById('mids').value && confirm('Remettre le socket à "mysqli.default_socket" ?')) {
			document.getElementById('sock').value = document.getElementById('mids').value;
		}
	}
}

-->
</script>
		
<form action="$urlStr" method="post">
<input type="hidden" id="mds" value="$mds">
<input type="hidden" id="mids" value="$mids">
<fieldset>
	<legend>Paramètres de connexion</legend>
	<p><label>Forcer une connexion par socket: <input type="checkbox" id="useSock" name="useSock" value="1" $chkSock onclick="majmode();"/></label></p>
	<p><label id="labsock" style="display:none;">Socket par défaut pour MySQL: <input type="text" id="sock" name="sock" value="$sock" size="80"/></label></p>
	<p><label id="labhost">Host : <input type="text" name="host" value="$host"/></label></p>
	<p><label id="labport">Port : <input type="text" name="port" value="$port"/></label></p>
	<p><label>Utilisateur : <input type="text" name="user" value="$user"/></label></p>
	<p><label>Mot de passe : <input type="password" name="password" value="$password"/></label></p>

FORM;

		// vérification de la présence de mysql
		$checked = isset($_POST['mysql']) ? 'checked' : '';
		if (!extension_loaded('mysql')) {
			echo "La librairie : 'mysql.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br \>";
		}
		if (extension_loaded('mysql')) {
			$html .= <<<FORM
	<p><label>Utiliser mysql : <input type="checkbox" id="mysql" name="mysql" $checked onclick="reloadmds();"/></label></p>

FORM;
} else {
	$html .= <<<FORM
	<p>(Extension mysql indisponible)</p>

FORM;
}


$html .= <<<FORM
</fieldset>
<fieldset>
	<legend><label>SQL</label></legend>
	<fieldset>
		<legend><label>Requête de test</label></legend>
		<p><label>Base de données : <input type="text" name="db" value="$db"/></label></p>
		<p><label>Table à lister : <input type="text" name="table" value="$table"/></label></p>
	</fieldset>
</fieldset>
<p><label>Test la connexion : <input type="submit" name="submit_mysql" value="Go"/></label></p>
</form>

<SCRIPT language="Javascript">
<!--
majmode();
// -->
</SCRIPT> 

FORM;
$this->setHtml($html);
	}


}

class OracleAction extends Action{

	function OracleAction(){}

	function execute()
	{
		switch($_REQUEST['action']){
			case 'form':
				$this->showForm();
				break;
			case 'connect':
				$this->showForm();
				$this->connect();
				break;
			default:

		} // switch
	}

	function connect()
	{
		if (!extension_loaded('oci8')) {
			echo "La librairie : 'oci8.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br \>";
		}
		
		extract($_POST);
		$html = $this->getHtml();
		if (!empty($user) && !empty($password)) {
			//$hostc = (!empty($port)) ? "$host:$port" : $host;

			if ($db_conn = ocilogon($user,$password,$service)) {
				$html .= "Connexion réussie<br />\n";

				if (!empty($sql)) {
					$html .= "<fieldset><legend>Exécution de la requête</legend>$sql</fieldset>\n";

					$stmt = ociparse($db_conn,$sql);
					ociexecute($stmt);
					while(ocifetchinto($stmt, $result, OCI_ASSOC)){
						$res[] = $result;
					}
					$html .= "<fieldset><legend>Résultat</legend>\n";
					$var = var_export($res, true);
					$html .= "$var</fieldset>\n";
				}
				ocilogoff($db_conn);
			} else{
				$html .= "Connexion impossible";
			}
		} else {
			$html .= "Vous devez renseigner a minima le nom de service, l'utilisateur et le mot de passe<br />";
		}
		$this->setHtml($html);
	}

	function showForm()
	{
		$service = isset($_POST['service']) ? $_POST['service'] : '';
		$user = isset($_POST['user']) ? $_POST['user'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$sql = isset($_POST['sql']) ? $_POST['sql'] : '';

		
		$url = Url::getInstance();
		$url->addQueryString('mode', 'oracle');
		$url->addQueryString('action', 'connect');

		$urlStr = $url->getUrl();
		$html = $this->getHtml();
		$html .= <<<FORM
<form action="$urlStr" method="post">
<fieldset>
	<legend>Paramètres de connexion</legend>
	<p><label>Service : <input type="text" name="service" value="$service"/></label></p>
	<p><label>Utilisateur : <input type="text" name="user" value="$user"/></label></p>
	<p><label>Mot de passe : <input type="password" name="password" value="$password"/></label></p>
</fieldset>
<fieldset>
	<legend><label>SQL</label></legend>
	<fieldset>
		<legend><label>Requête de test</label></legend>
		<p><label>Requête SQL : <textarea cols="50" rows="5" name="sql" >$sql</textarea> </label></p>
	</fieldset>
</fieldset>
<p><label>Test la connexion : <input type="submit" name="submit_oracle" value="Go"/></label></p>
</form>
FORM;
		$this->setHtml($html);
}


}

class LdapAction extends Action{

	function LdapAction(){}

	function execute()
	{
		switch($_REQUEST['action']){
			case 'form':
				$this->showForm();
				break;
			case 'connect':
				$this->connect();
				$this->showForm();
				break;
			default:

		} // switch
	}

	function connect()
	{
		// putenv ne fonctionne pas avec le php.ini standard de PHP 5.2.9 (variable LDAPCONF non ecrasable)
		//putenv("LDAPCONF=/etc/openldap/ldap.conf");
		
		$_SESSION['ldapversion'] = $_POST['ldapversion'];

		extract($_POST);

		$html = $this->getHtml();

		$html .= "<fieldset>\n<legend>Résultat de l'authentification</legend>\n";

		$ds=ldap_connect($host);
		$html .= "Le r&eacute;sultat de connexion est : ". $ds ."<br/>";

		if (ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $_SESSION['ldapversion'])) {
			$html .= "Utilisation de LDAPV" . $_SESSION['ldapversion'] . '<br/>';
		} else {
			$html .= "Impossible de modifier la version du protocole en LDAPV" . $_SESSION['ldapversion'] . '<br/>';
		}

		if ($ds) {

			$r = @ldap_bind($ds, $app_user, $app_password);

			$rok = ($r) ? 'Réussie' : 'Echouée';
			$html .= "Authentification avec le compte applicatif : ". $rok . "<br/>";

			// Recherche par nom
			if ($r) {
				$html .= "<br/>Recherche de ($searchField=$user) sur la base '$searchBase' ...<br/>";

				$sr = ldap_search($ds,$searchBase, "$searchField=$user");
				$html .= "Le r&eacute;sultat de la recherche est : ".$sr."<br/>";
	
				$nb_entrees = ldap_count_entries($ds,$sr);
				if ($nb_entrees) {
					$html .= "Le nombre d'entr&eacute;es retourn&eacute;es est $nb_entrees<br/>";
					$html .= "<br/>Lecture des entr&eacute;es ...<br/>";
					$info = ldap_get_entries($ds, $sr);
					$html .= "Authentification de l'utilisateur avec le dn {$info[0]['dn']} ...<br/>";
					$r=@ldap_bind($ds, $info[0]['dn'], $password);
					$rok = ($r) ? 'Réussie' : 'Echouée';
					$html .= "Authentification avec le compte utilisateur : ". $rok . "<br/>";
				} else {
					$html .= "Aucune entrée retournée<br/>";
				}
	
				/* $html .= "Donn&eacute;es pour ".$info["count"]." entr&eacute;es:<p>";
	
				for ($i=0; $i<$info["count"]; $i++) {
				$html .= "dn est : ". $info[$i]["dn"] ."<br>";
				$html .= "premiere entree cn : ". $info[$i]["cn"][0] ."<br>";
				$html .= "premier email : ". $info[$i]["mail"][0] ."<p>";
				}*/
			}
			$html .= "<br/>Fermeture de la connexion";
			ldap_close($ds);

		} else {
			$html .= "<h4>Impossible de se connecter au serveur LDAP.</h4>";
		}
		$html .= "</fieldset>";
		$this->setHtml($html);
	}

	function showForm()
	{
		$searchBase = (empty($searchBase)) ? 'c=fr' : $searchBase;
		$searchField = (empty($searchField)) ? 'uid' : $searchField;
		$ldapconf = (empty($ldapconf)) ? getenv("LDAPCONF") : $ldapconf;

		if (!isset($_SESSION['ldapversion'])) {
			$_SESSION['ldapversion'] = 3;
		}
		
		$checkedLdapV3 = ($_SESSION['ldapversion'] == 3 || $_SESSION['ldapversion'] != 2) ? 'checked="checked"' : '';
		$checkedLdapV2 = ($_SESSION['ldapversion'] == 2) ? 'checked="checked"' : '';

		$host = isset($_POST['host']) ? $_POST['host'] : '';
		$app_user = isset($_POST['app_user']) ? $_POST['app_user'] : '';
		$app_password = isset($_POST['app_password']) ? $_POST['app_password'] : '';
		$user = isset($_POST['user']) ? $_POST['user'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		
		$url = Url::getInstance();
		$url->addQueryString('mode', 'ldap');
		$url->addQueryString('action', 'connect');

		$urlStr = $url->getUrl();
		$html = $this->getHtml();
		$html .= <<<FORM
<form action="$urlStr" method="post">
<fieldset>
	<legend>Paramètres de connexion</legend>
	<p><label>Host : <input type="text" name="host" value="$host" size="50" /></label>&nbsp;$ldaps_ok</p>
	<p><label>Fichier ldap.conf : <input type="text" name="ldapconf" value="$ldapconf" size="60" disabled /></label></p>
	<p>
		<label>
			Utilisateur : 
			<input onfocus="showHelp('app_userHelp')" onblur="leaveHelp('app_userHelp')" type="text" name="app_user" value="$app_user" size="60" />
			<span id="app_userHelp" class="help" style="visibility:hidden">Le DN du compte applicatif</span>
		</label>
	</p>
	<p><label>Mot de passe : <input type="password" name="app_password" value="$app_password"/></label></p>
	<p>Version de LDAP : V3 <input type="radio" $checkedLdapV3 name="ldapversion" value="3"/> V2 <input type="radio" $checkedLdapV2 name="ldapversion" value="2"/></p>
</fieldset>
<fieldset>
	<legend><label>Recherche d'utilisateur</label></legend>
	<p><label>Base de recherche : <input type="text" name="searchBase" value="$searchBase"/></label></p>
	<p>
		<label>
			Champ de recherche : 
			<input onfocus="showHelp('searchFieldHelp')" onblur="leaveHelp('searchFieldHelp')" type="text" name="searchField" value="$searchField"/>
			<span id="searchFieldHelp" class="help" style="visibility:hidden">Le champ sur lequel est effectué la recherche. Ex avec 'uid' : 'uid=prenom.nom@edfgdf.fr'"</span>
		</label>
	</p>
</fieldset>
<fieldset>
	<legend><label>Authentification utilisateur</label></legend>
	<p><label>Utilisateur : <input type="text" name="user" value="$user"/></label></p>
	<p><label>Mot de passe : <input type="password" name="password" value="$password"/></label></p>
</fieldset>
<p><label>Test la connexion : <input type="submit" name="submit_ldap" value="Go"/></label></p>
</form>
FORM;
		$this->setHtml($html);
}

}

class ConfAction extends Action{

	function ConfAction(){}

	function execute()
	{
		switch($_REQUEST['action']){
			case 'form':
				$this->showForm();
				break;
			case 'save':
				$html = "<div class=\"message\">Les modifications ont été enregistrées</div><br />";
				$this->setErrorsParams();
				$this->setErrorLevel();
				$this->showForm();
				break;
			default:
				$this->showForm();
		} // switch
	}

	function setErrorsParams()
	{
		if (!empty($_POST['display_errors'])) {
			$_SESSION['display_errors'] = $_POST['display_errors'];
			ini_set('display_errors', 1);
		} else {
			$_SESSION['display_errors'] = null;
			ini_set('display_errors', 0);
		}
		if (!empty($_POST['log_errors'])) {
			$_SESSION['log_errors'] = $_POST['log_errors'];
			ini_set('log_errors', 1);
		} else {
			$_SESSION['log_errors'] = null;
			ini_set('log_errors', 0);
		}
	}

	function setErrorLevel()
	{
		$_SESSION['error_reporting'] = $_POST['errorReporting'];
		$GLOBALS['e'] = error_reporting($_SESSION['error_reporting']);
		$GLOBALS['e'] = error_reporting($_SESSION['error_reporting']);
	}

	function showForm()
	{
		$url = Url::getInstance();
		$url->addQueryString('mode', 'conf');
		$url->addQueryString('action', 'save');

		$checkedDisplayErrors = (!empty($_SESSION['display_errors'])) ? 'checked="checked"' : '';
		$checkedLogErrors = (!empty($_SESSION['log_errors'])) ? 'checked="checked"' : '';

		$e = $GLOBALS['e'];
		$checked['E_ALL'] = (($e & 2047)) ? 'checked="checked"': '';
		$checked['E_COMPILE_ERROR'] = (($e & 64)) ? 'checked="checked"': '';
		$checked['E_COMPILE_WARNING'] = (($e & 128)) ? 'checked="checked"': '';
		$checked['E_CORE_ERROR'] = (($e & 16)) ? 'checked="checked"': '';
		$checked['E_CORE_WARNING'] = (($e & 32)) ? 'checked="checked"': '';
		$checked['E_ERROR'] = (($e & 1)) ? 'checked="checked"': '';
		$checked['E_NOTICE'] = (($e & 8)) ? 'checked="checked"': '';
		$checked['E_PARSE'] = (($e & 4)) ? 'checked="checked"': '';
		$checked['E_USER_ERROR'] = (($e & 256)) ? 'checked="checked"': '';
		$checked['E_USER_NOTICE'] = (($e & 1024)) ? 'checked="checked"': '';
		$checked['E_USER_WARNING'] = (($e & 512)) ? 'checked="checked"': '';
		$checked['E_WARNING'] = (($e & 2)) ? 'checked="checked"': '';

		$urlStr = $url->getUrl();
		$html = $this->getHtml();
		$html .= <<<FORM
<script type="text/javascript">

var errors = [ "E_COMPILE_ERROR", "E_COMPILE_WARNING", "E_CORE_ERROR", "E_CORE_WARNING", "E_ERROR", "E_NOTICE", "E_PARSE", "E_USER_ERROR", "E_USER_NOTICE", "E_USER_WARNING", "E_WARNING" ];

function calculeNiveauErreur(el){
	
	var errorReporting = document.getElementById("errorReporting").value;

	//for ( var i = 0 ; i < errors.length ; i++ ){
		if (el.checked)
			errorReporting = parseInt(errorReporting) + parseInt(el.value);
		else
			errorReporting = parseInt(errorReporting) - parseInt(el.value);
	//}
	document.getElementById("errorReporting").value = errorReporting;
}

function setE_All(el){
	if (el.checked){
		for ( var i = 0 ; i < errors.length ; i++ )
			document.getElementById(errors[i]).checked = true;
		document.getElementById("errorReporting").value = 2047;
	} else {
		for ( var i = 0 ; i < errors.length ; i++ )
			document.getElementById(errors[i]).checked = false;
		document.getElementById("errorReporting").value = 0;
	}
	
}

function toggleErrorList(el){
	errorListElement = document.getElementById("errorList");
	
	if (errorListElement.style.visibility=="visible" || errorListElement.style.visibility==''){
		errorListElement.style.visibility="hidden";
		errorListElement.style.display="none";
		el.firstChild.nodeValue = "Afficher la liste détaillée des niveau d erreurs";
	} else {
		errorListElement.style.visibility="visible";
		errorListElement.style.display="inline";
		el.firstChild.nodeValue = "Masquer la liste détaillée des niveau d erreurs";
	}
}

function submitForm(formEl){
	formEl.action = formEl.action + "&error_reporting=" + document.getElementById("errorReporting").value
	formEl.submit();
}

</script>
<form action="$urlStr" method="post">
<fieldset>
<legend>Paramétrage de la gestion des erreurs</legend>
	<label for="display_errors">display_errors</label>
	<input type="checkbox" $checkedDisplayErrors id="display_errors" name="display_errors" />
	<label for="log_errors">log_errors</label>
	<input type="checkbox" $checkedLogErrors id="log_errors" name="log_errors" />
</fieldset>
<fieldset>
<legend>Niveau d'erreur</legend>
<div>
	<a href="#" onClick="toggleErrorList(this)">Masquer la liste détaillée des niveau d'erreurs</a>
</div>
<div id="errorList">
	<div class="errorCheckbox">
		<label for="E_ALL">E_ALL</label>
		<input type="checkbox" {$checked['E_ALL']} id="E_ALL" onclick="setE_All(this)" />
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_COMPILE_ERROR']} id="E_COMPILE_ERROR" value="64" onclick="calculeNiveauErreur(this)" />
		<label for="E_COMPILE_ERROR">E_COMPILE_ERROR</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_COMPILE_WARNING']} id="E_COMPILE_WARNING" value="128" onclick="calculeNiveauErreur(this)" />
		<label for="E_COMPILE_WARNING">E_COMPILE_WARNING</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_CORE_ERROR']} id="E_CORE_ERROR" value="16" onclick="calculeNiveauErreur(this)" />
		<label for="E_CORE_ERROR">E_CORE_ERROR</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_CORE_WARNING']} id="E_CORE_WARNING" value="32" onclick="calculeNiveauErreur(this)" />
		<label for="E_CORE_WARNING">E_CORE_WARNING</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_ERROR']} id="E_ERROR" value="1" onclick="calculeNiveauErreur(this)" />
		<label for="E_ERROR">E_ERROR</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_NOTICE']} id="E_NOTICE" value="8" onclick="calculeNiveauErreur(this)" />
		<label for="E_NOTICE">E_NOTICE</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_PARSE']} id="E_PARSE" value="4" onclick="calculeNiveauErreur(this)" />
		<label for="E_PARSE">E_PARSE</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_USER_ERROR']} id="E_USER_ERROR" value="256" onclick="calculeNiveauErreur(this)" />
		<label for="E_USER_ERROR">E_USER_ERROR</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_USER_NOTICE']} id="E_USER_NOTICE" value="1024" onclick="calculeNiveauErreur(this)" />
		<label for="E_USER_NOTICE">E_USER_NOTICE</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_USER_WARNING']} id="E_USER_WARNING" value="512" onclick="calculeNiveauErreur(this)" />
		<label for="E_USER_WARNING">E_USER_WARNING</label>
	</div>
	<div class="errorCheckbox">
		<input type="checkbox" {$checked['E_WARNING']} id="E_WARNING" value="2" onclick="calculeNiveauErreur(this)" />
		<label for="E_WARNING">E_WARNING</label>
	</div>
</div>
<input type="text" name="errorReporting" id="errorReporting" value="$e" />

</fieldset>

<input type="submit" name="submit" value="Enregistrer"/>
</form>
FORM;
		$this->setHtml($html);
}
}

class ExtAction extends Action{

	function ExtAction(){}

	function execute()
	{
		switch($_REQUEST['action']){
			default:
				$this->showResult();
		} // switch
	}

	function showResult()
	{
		define ("PHP4", substr(phpversion(), 0, 1) == '4');
		define ("PHP5", substr(phpversion(), 0, 1) == '5');

		$html = $this->getHtml();

		// par defaut la présence d'une librairie est testée avec extension_loaded()
		// ce tableau permet de définir pour certaines librairies un code source dédié a ce test
		$arrTestExistLib = array();

		// tableaux indexés par nom de librairie et contenant un bout de code pour en tester la bonne exécution
		// le résultat de l'exécution doit valoir true ou false en cas de succès ou d'échec

		$arrLinkStatique = array();
		$arrLinkDynamique = array();
		$arrExtension = array();

		/************************************************************************************************
		Link statique
		************************************************************************************************/
		$arrLinkStatique['calendar'] = '$tmp = cal_info(0);return isset($tmp["months"]["1"]);';
		
		$arrLinkStatique['ctype'] = 'return ctype_xdigit("abcdef") && ctype_space(" \t");';   // pas de test d execution encore cree

		$arrLinkStatique['date'] = 'return date("U") == time();';   // pas de test d execution encore cree

		$arrLinkStatique['dom'] = '$doc = new DOMDocument(); $root = $doc->createElement("root"); $doc->appendChild($root); return $doc->saveXML($root) == "<root/>";';   // pas de test d execution encore cree

		$arrLinkStatique['filter'] = 'return filter_var("test@test.com", FILTER_VALIDATE_EMAIL) && !filter_var("test.com", FILTER_VALIDATE_EMAIL);';   // pas de test d execution encore cree

		$arrTestExistLib['freetype'] = '$arr = gd_info(); return $arr["FreeType Support"];';
		$arrLinkStatique['freetype'] = 'return is_array(imageftbbox(10,0,"arial","test"));';

		$arrLinkStatique['ftp'] = 'return ftp_connect("localhost") === false;';	// pas de test d execution car il faudrait etablir une connexion

		$arrLinkStatique['gd'] = 'return imagecreatetruecolor(10,10);';

		$arrTestExistLib['gd-native-ttf'] = 'return extension_loaded("gd");';
		$arrLinkStatique['gd-native-ttf'] = 'return is_array(imagettfbbox(10,0,"arial","test"));';	// validité de ce test

		//$arrExtension['mhash'] = 'return bin2hex(mhash(MHASH_SHA1, "what do ya want for nothing?","Jefe")) == "effcdf6ae5eb2fa2d27416d5f184df9c259a7c79";';

		
		$arrLinkStatique['hash'] = '$ctx = hash_init("md5"); hash_update($ctx, "H me"); return strlen(hash_final($ctx)) == 32;';   // pas de test d execution encore cree

		$arrLinkStatique['iconv'] = 'return iconv("ISO-8859-1","UTF-8","Ceci est un test.");';

		$arrLinkStatique['json'] = '$obj = array(1,2,3,4,5); return json_encode($obj) == "[1,2,3,4,5]";';   // pas de test d execution encore cree

		$arrTestExistLib['memory_limit'] = 'return function_exists("memory_get_usage");';
		$arrLinkStatique['memory_limit'] = 'return (memory_get_usage() > 0);';

		// récupération du module de gestion de session pour sa restauration
		$strOrigSessionModule = session_module_name();
		$sessionId = session_id();
		$arrTestExistLib['mm'] = 'session_write_close();
		session_module_name("mm");$res=session_module_name();
		session_module_name("' . $strOrigSessionModule . '");session_id("' . $sessionId . '");session_start();
		return ($res=="mm");';

		$arrLinkStatique['mm'] = 'session_write_close();
		session_module_name("mm");$res=session_start("test");session_write_close();
		session_module_name("' . $strOrigSessionModule . '");session_id("' . $sessionId . '");session_start();
		return $res;';

		$arrLinkStatique['mysql'] = 'return mysql_connect("localhost", "root", "") === false;';

		$arrLinkStatique['openssl'] = 'return is_resource(openssl_pkey_new());';

		$arrLinkStatique['pcre'] = 'return preg_match("/b/", "abc");';

		$arrLinkStatique['pdo'] = '$drivers = pdo_drivers(); return is_array($drivers) && count($drivers) > 0;';   // pas de test d execution encore cree

		$arrLinkStatique['posix'] = 'return posix_getcwd() == dirname(__FILE__);';   // pas de test d execution encore cree

		$arrLinkStatique['reflection'] = '$func = new ReflectionFunction("strlen"); return $func->getName() == "strlen";';   // pas de test d execution encore cree

		$arrLinkStatique['session'] = '$_SESSION["dummy"] = "dummy"; return session_is_registered("dummy");';   // pas de test d execution encore cree

		$arrLinkStatique['simplexml'] = '$dom = simplexml_load_string($strExempleXML); return $dom->children();';   // pas de test d execution encore cree

		$arrLinkStatique['sockets'] = 'return socket_create(AF_INET, SOCK_STREAM, SOL_TCP);';   // pas de test d execution encore cree

		$arrLinkStatique['spl'] = '$spl = new SplFileInfo(__FILE__); $info = $spl->getPathInfo(); return $info->getPathName() == dirname(__FILE__);';   // pas de test d execution encore cree

		$arrLinkStatique['standard'] = 'return strlen("test") == 4;'; /*????*/  // pas de test d execution encore cree

		$arrLinkStatique['tokenizer'] = '$tokens = token_get_all("<?php echo; ?>"); return token_name($tokens[1][0]) == "T_ECHO";';   // pas de test d execution encore cree

		// fin Link statique


		/************************************************************************************************
		Link dynamique
		************************************************************************************************/

		$arrTestExistLib['gif'] = '$arr = gd_info(); return $arr["JPG Support"];';
		$arrLinkDynamique['gif'] = 'return imagetypes() & IMG_GIF;';
		
		$arrTestExistLib['jpeg'] = '$arr = gd_info(); return $arr["JPG Support"];';
		$arrLinkDynamique['jpeg'] = 'return imagetypes() & IMG_JPG;';

		$arrLinkDynamique['ldap'] = 'return ldap_dn2ufn("o=edf,c=fr");';

		$arrTestExistLib['png'] = '$arr = gd_info(); return $arr["PNG Support"];';
		$arrLinkDynamique['png'] = 'return imagetypes() & IMG_PNG;';

		$arrLinkDynamique['sockets'] = 'return socket_create(AF_INET, SOCK_STREAM, SOL_TCP);';

		$arrLinkDynamique['xml'] = '$simple = "<para><note>Simple Note</note></para>";
		$p = xml_parser_create();
		return xml_parse_into_struct($p, $simple, $vals, $index);
		';

		$arrLinkDynamique['xmlreader'] = '$xml = new XMLReader();
		$xml->XML($strExempleXML);
		return $xml->read();
		';

		$arrLinkDynamique['xmlwriter'] = '$xml = new XMLWriter(); $xml->openMemory(); $xml->writeElement("root", "test"); $out = $xml->outputMemory(); return $out == "<root>test</root>";'; // pas de test d execution encore cree

		$arrLinkDynamique['zlib'] = 'return gzdeflate("Compresse moi", 9);';

		// fin link dynamique

		/************************************************************************************************
		Extensions
		************************************************************************************************/
		if (!extension_loaded('apc')) {
                        echo "La librairie : 'apc.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('bcmath')) {
                        echo "La librairie : 'bcmath.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		 if (!extension_loaded('bz2')) {
                        echo "La librairie : 'bz2.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('curl')) {
                        echo "La librairie : 'curl.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('eAccelerator')) {
                        echo "La librairie : 'eaccelerator.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('geoip')) {
                        echo "La librairie : 'geoip.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('gettext')) {
						echo "La librairie : 'gettext.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
				}
		if (!extension_loaded('imap')) {
						echo "La librairie : 'imap.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
				}
		if (!extension_loaded('intl')) {
						echo "La librairie : 'intl.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
				}
		if (!extension_loaded('mailparse')) {
						echo "La librairie : 'mailparse.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
				}
		if (!extension_loaded('mbstring')) {
                        echo "La librairie : 'mbstring.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('mcrypt')) {
						echo "La librairie : 'mcrypt.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
				}
		if (!extension_loaded('mysqli')) {
						echo "La librairie : 'mysqli.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
				}
		if (!extension_loaded('mysqlnd_ms')) {
						echo "La librairie : 'mysqlnd_ms.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
				}
		if (!extension_loaded('oci8')) {
						echo "La librairie : 'oci8.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
				}
		if (!extension_loaded('pdo_mysql')) {
						echo "La librairie : 'pdo_mysql.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
				}
		if (!extension_loaded('pdo_oci')) {
                        echo "La librairie : 'pdo_oci.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('phar')) {
                        echo "La librairie : 'phar.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('soap')) {
                        echo "La librairie : 'soap.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('ssh2')) {
                        echo "La librairie : 'ssh2.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('suhosin')) {
                        echo "La librairie : 'suhosin.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />"; 
                }
		if (!extension_loaded('wsf')) {
                        echo "La librairie : 'wsf.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
		}
		if (!extension_loaded('xdebug')) {
                        echo "La librairie : 'xdebug.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }
		if (!extension_loaded('xsl')) {
                        echo "La librairie : 'xsl.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
		}		
		if (!extension_loaded('zip')) {
                        echo "La librairie : 'zip.so' n'est pas charger. Vous devez la charger pour pouvoir effectuer ce test<br />";
                }

		$arrExtension['apc'] = 'return extension_loaded("apc");';

		$arrExtension['bcmath'] = 'return extension_loaded("bcmath");';
		
		$arrExtension['bz2'] = 'return bzdecompress(bzcompress("test")) == "test";';

		$arrExtension['curl'] = '$c = curl_init("http://".$_SERVER["HTTP_HOST"]."/test_souche.php"); curl_setopt($c, CURLOPT_RETURNTRANSFER, true); $out = curl_exec($c); curl_close($c); return preg_match("/<body>/", $out, $matches);';

		$arrExtension['eAccelerator'] = 'return is_array(eaccelerator_info());';

		$arrExtension['geoip'] = 'return extension_loaded("geoip");';
		
		$arrExtension['gettext'] = 'return textdomain("null");';
		
		$arrExtension['imap'] = 'return extension_loaded("imap");';
		
		$arrExtension['intl'] = 'return extension_loaded("intl");';
		
		$arrExtension['mailparse'] = 'return extension_loaded("mailparse");';
				
		$arrExtension['mbstring'] = 'return mb_check_encoding(utf8_encode("écran de fumée"), "utf-8");';

		$arrExtension['mcrypt'] = 'return mcrypt_encrypt(MCRYPT_3DES, "key", "test", MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB), MCRYPT_RAND));';

		$arrExtension['mysqli'] = 'return mysqli_init();';
		
		$arrExtension['mysqlnd_ms'] = 'return extension_loaded("mysqlnd_ms");';

		$arrExtension['oci8'] = 'return oci_connect("root", "") === false;';

		$arrExtension['pdo_mysql'] = '$drivers = pdo_drivers(); return in_array("mysql", $drivers);';

		$arrExtension['pdo_oci'] = '$drivers = pdo_drivers(); return in_array("oci", $drivers);';
		
		$arrExtension['phar'] = 'return extension_loaded("phar");';

		$arrExtension['soap'] = 'return extension_loaded("soap");';
		
		$arrExtension['ssh2'] = 'return extension_loaded("ssh2");';
		
		$arrExtension['suhosin'] = 'return extension_loaded("suhosin");';

		$arrExtension['wsf'] = '$w = new WSClient(); return is_object($w);';

		$arrExtension['xdebug'] = 'return is_array(xdebug_get_function_stack());';

		$arrExtension['xsl'] = '$xsl = new XSLTProcessor(); $xsl->setParameter("", "key", "value"); return $xsl->getParameter("", "key") == "value";';
		/*
		// CHargement du source XML
		$xml = new DOMDocument;
		$xml->loadXML($strExempleXML);
		$xsl = new DOMDocument;
		$xsl->loadXML($strExempleXSL);
		// Configuration du transformateur
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl); // attachement des regles xsl
		return $proc->transformToXML($xml);
		';
		*/

		$arrExtension['zip'] = '$z = new ZipArchive(); $z->open("test_souche.zip", ZIPARCHIVE::CREATE); $z->addFile(__FILE__); $i = $z->numFiles; $z->close(); return $i == 1;';

		// fin Extensions

		// lancement des test
		$this->testLibrairies($arrLinkStatique, $arrTestExistLib);
		$this->testLibrairies($arrLinkDynamique, $arrTestExistLib);
		$this->testLibrairies($arrExtension, $arrTestExistLib);

		// affichage du résultat
		$html .= "<h2>Librairies statiques : obligatoirement présentes</h2>";
		$this->afficheExtensions($html, $arrLinkStatique, true);

		$html .= "<br/><br/><h2>Librairies dynamiques : obligatoirement présentes</h2>";
		$this->afficheExtensions($html, $arrLinkDynamique, true);

		$html .= "<br/><br/><h2>Extensions/Modules PHP chargé(e)s dans le fichier php.ini de l'instance Apache : optionnel(le)s</h2>";
		$this->afficheExtensions($html, $arrExtension, false);

		$this->setHtml($html);
//		print_r(get_loaded_extensions());
//		print_r(get_defined_functions());
//		print_r(get_declared_classes());
	}

	// test des librairies (présence + fonctionnement)
	function testLibrairies(&$arrExt, &$arrTestExistLib) {
		// fichiers de test pour les modules XML et XSL
		$strExempleXML = '
<collection>
 <cd>
  <title>Fight for your mind</title>
  <artist>Ben Harper</artist>
  <year>1995</year>
 </cd>
 <cd>
  <title>Electric Ladyland</title>
  <artist>Jimi Hendrix</artist>
  <year>1997</year>
 </cd>
</collection>
';
		$strExempleXSL = '
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:param name="owner" select="\'Nicolas Eliaszewicz\'"/>
 <xsl:output method="html" encoding="iso-8859-1" indent="no"/>
 <xsl:template match="collection">
  Hey! Welcome to <xsl:value-of select="$owner"/>\'s sweet CD collection! 
  <xsl:apply-templates/>
 </xsl:template>
 <xsl:template match="cd">
  <h1><xsl:value-of select="title"/></h1>
  <h2>by <xsl:value-of select="artist"/> - <xsl:value-of select="year"/></h2>
  <hr />
 </xsl:template>
</xsl:stylesheet>
';
		foreach ($arrExt as $strNom => $strCode) {

			// test de présence de la librairie
			if (isset($arrTestExistLib[$strNom])) {	// code spécifique pour tester l'existance de l'extension
				$boolExtensionLoaded = ($arrTestExistLib[$strNom] != '') ? eval($arrTestExistLib[$strNom]) : null;
			} else {					// code standard
				$boolExtensionLoaded = extension_loaded($strNom);
			}

			// test de fonctionnement de la librairie
			$boolFonctionMarche = ($boolExtensionLoaded && $strCode != '') ? eval($strCode) : null;

			$arrExt[$strNom] = array ($strCode, $boolExtensionLoaded, $boolFonctionMarche);
		}
	}

	// affichage du résultat pour un type d'extensions (1 = statique, 2 = dynamique)
	function afficheExtensions(&$html, &$arrExt, $boolObligatoire) {
		// en cas d'absence d'une librairie obligatoire, une classe spéciale est utilisée
		$classeEchecTest = $boolObligatoire ? 'ko' : '';

		$html .= "
<table>
<tr><th>Extension</th><th>Détectée</th><th>Test</th><th>Code utilisé pour le test</th></tr>";

		// affichage du résultat
		foreach ($arrExt as $nom => $arrTmp) {
			if (is_null($arrTmp[1])) {
				$boolExtensionLoaded = '<td></td>';
			} elseif ($arrTmp[1] == true) {
				$boolExtensionLoaded = '<td class="ok">Oui</td>';
			} else {
				$boolExtensionLoaded = '<td class="' . $classeEchecTest . '">Non</td>';
			}

			if (is_null($arrTmp[2])) {
				$strFonctionMarche = '<td></td>';
			} elseif ($arrTmp[2] == true) {
				$strFonctionMarche = '<td class="ok">Réussi</td>';
			} else {
				$strFonctionMarche = '<td class="ko">Echec</td>';
			}

			$strSourceTest = highlight_string($arrTmp[0], true);
			if ($nom == 'eAccelerator') $nom .= '<br /><b>A NE PLUS UTILISER, SAUF PROBLEMES AVEC APC comme solution de cache PHP</b>';
			if ($nom == 'pdo') $nom .= '<br /><b>Test en échec tant que le module pdo_mysql ou pdo_oci n est pas chargé</b>';
			$html .= "
<tr><td>$nom</td>$boolExtensionLoaded $strFonctionMarche<td>$strSourceTest</td></tr>";
		}

		$html .= "
</table>";
	}
}

class ErrorAction extends Action{

	var $msg = '';

	function ErrorAction($msg){
		$this->msg = $msg;
	}

	function execute()
	{
		$html = $this->getHtml();
		$html .= $this->msg;
		$this->setHtml($html);
	}

}

?>
</body>
</html>
