# SingleSignon
PHP Class to aid in single sign-on across multiple servers using the same domain.


To check if a user is signed in:
```
$signon = new SingleSignon();
$userId = $signon->checkCookie();
if($userId){
	// user is logged in and this is their id
}
else{
	// user is not logged in or they are exipred
}
```

To set a user as signed in:
```
$signon = new SingleSignon();
$signon->setCookie($userId);
```

To sign a user out:
```
$signon = new SingleSignon();
$signon->deleteCookie();
```


NOTE: You need to be using ssl for the cookie to be read.
