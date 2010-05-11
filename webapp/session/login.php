<?php 
session_start();

// set up
chdir("..");


require_once ("init.php");

$session = new Session();
if ($session->isLoggedIn()) {
    header("Location: ../index.php");
}

$od = new OwnerDAO($db);
$user_email = mysql_real_escape_string($_POST['email']);
$s = new SmartyThinkTank();
$s->caching=false;

if ($_POST['Submit'] == 'Log In') {
    $result = $od->getForLogin($user_email);
    if (!$result) {
        $emsg = "Incorrect email or password";
    } elseif (!$session->pwdCheck($_POST['pwd'], $result['pwd'])) {
        $emsg = "Incorrect email or password";
    } else {
        // this sets variables in the session
        $session->completeLogin($result);
		$od->updateLastLogin($user_email);
        if (isset($_GET['ret']) && ! empty($_GET['ret'])) {
            header("Location: $_GET[ret]");
        } else {
            header("Location: ".$THINKTANK_CFG['site_root_path']);
        }
        exit();
    }
}
if (isset($_GET["smsg"])){
    $smsg = $_GET["smsg"];
}
if (isset($emsg)) {
    $s->assign('errormsg', $emsg);
} elseif (isset($smsg)) {
    $s->assign('successmsg', $smsg);
}
if (isset($_POST["email"])){
	$s->assign('email', $_POST["email"]);
}

$db->closeConnection($conn);
$cfg = new Config();
$s->assign('cfg', $cfg);
$s->display('session.login.tpl');

?>
