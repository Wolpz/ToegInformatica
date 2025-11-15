
<?php
if( !(array_key_exists('email', $_REQUEST) && array_key_exists('internet', $_REQUEST))){
    $message = "Vul beide velden in.";
    $email = "";
    $site = "";
}
else{
    $email = $_REQUEST['email'];
    $site = $_REQUEST['internet'];

    // Checking email validity
    if(checkEmailValid($_REQUEST['email']))
        $mail_valid = "VALID ";
    else
        $mail_valid = "NOT VALID ";

    // Checking site validity
    if(checkSiteValid($_REQUEST['internet']))
        $site_valid = "VALID ";
    else
        $site_valid = "NOT VALID ";

    $message = 'E-mail: '.$mail_valid.'<br>'.'Site: '.$site_valid;
}
?>
<html>
<head>
    <title>Opdracht c</title>
</head>
<body>

<?php echo "<div>".$message."<br></div>";
?>
<form>
    <input type="text" name="email" placeholder="e-mailadres" value="<?php echo htmlspecialchars($email); ?>">
    <input type="text" name="internet" placeholder="Webadres" value="<?php echo htmlspecialchars($site); ?>">
    <input type="submit" value="zend">
</form>

</body>
</html>

<?php
function checkEmailValid($email){
    // Email address syntax from: https://snov.io/knowledgebase/what-is-a-valid-email-address-format/

    // 1 @ symbol present
    if(preg_match_all('/@/', $email) != 1)
        return false;
    // Checking for general illegal characters
    if(preg_match('/[^[0-9a-z_\-@\.]/i', $email))
        return false;

    $email = explode('@', $email);
    $username = $email[0];
    $domain = $email[1];

    // Checking for consecutive periods
    if(preg_match('/\.{2,}/', $username))
        return false;
    // Search for . at start or end of username
    if(preg_match('/^\.|\.$/', $username))
        return false;
    // Special characters should be followed by letter or number
    if(preg_match('/[_\-@\.][^0-9a-z]|[_\-@\.]$/i', $username))
        return false;

    // Check if extension is 2 letters
    if(!preg_match('/\.[a-z]{2,}$/i', $domain))
        return false;
    // Search for illegal underscore in domain
    if(preg_match('/_/', $domain))
        return false;
    // Check for dashes at beginning or end of domain name
    if(preg_match('/^-|-$/', preg_replace('/\..{2,}$/', '', $domain) ) )
        return false;

    return true;
}

function checkSiteValid($site){
    // URL syntax from: https://www.makeuseof.com/regular-expressions-validate-url/

    // Check for protocol
    if(!preg_match('/^(http|https):\/\//i', $site))
        return false;

    $domain = preg_replace('/^(http|https):\/\//i', '', $site);
    // Domain should be between 2 and 255
    if(strlen($domain) > 255 || strlen($domain) < 2 )
        return false;
    // Domain should contain only alphanumeric or special
    if(preg_match('/[^!-~]/', $domain))
        return false;
    // No nameless directories
    if(preg_match('/\/\//', $domain))
        return false;

    $domain = explode('/', $domain);
    // Topdomain should be between 2 and 6 letters
    if(!preg_match('/\.[a-z]{2,6}$/i', $domain[0])) //|| (strlen($topdomain) > 6) || (strlen($topdomain) < 2))
        return false;

    return true;
}
?>