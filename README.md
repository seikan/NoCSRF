# NoCSRF

A simple PHP class to stop Cross-Site Request Forgery (CSRF).



## Usage

### Configuration

> \$csrf = new NoCSRF( [**array** $options] );

**Options**

**string** `key` - Defines a unique key to store the security token in session.

**bool** `lock_ip` - Enables IP verification so form can only posted from same client IP.

**int** `timer` - Sets the token valid time in seconds.

```php
$csrf = new NoCSRF([
	'lock_ip'	=> true, // Make sure form is posting from same client IP
  	'timer'		=> 1800, // Token expires after 30 minutes
]);
```



### Get Key

Gets current session key.

> **string** \$csrf->getKey( );

```php
echo $csrf->getKey();
```



### Get Token

Gets current generated token.

> **string** \$csrf->getToken( );

```php
echo $csrf->getToken();
```



### Delete Token

Deletes current token to generate new token. 

> \$csrf->deleteToken( );

```php
$csrf->deleteToken();
```



### Render HTML

Renders a hidden text field to store the security token.

**Notes:** Must put within `<form></form>` tags.

> **string** \$csrf->renderHTML( );

```php
<form method="post">
  	<input type="text" name="email" value="">
  	<?php
	echo $csrf->renderHTML();
	?>
</form>
```



### Validate

Validates form post to make sure no CSRF.

> **int** \$csrf->validate( );

```php
if (!empty($_POST)) {
  switch($csrf->validate()) {
    case NoCSRF::PASSED:
      echo 'Passed!';
      break;
      
    case NoCSRF::POST_INPUT_NOT_FOUND:
      echo 'No security token is submited.';
      break;
      
    case NoCSRF::TOKEN_NOT_FOUND:
      echo 'Token is not generated.';
      break;
      
    case NoCSRF::TOKEN_INVALID:
      echo 'Token not match, CSRF detected.';
      break;
      
    case NoCSRF::TOKEN_EXPIRED:
      echo 'The token submitted is already expired.';
      break;
      
    case NoCSRF::IP_CHANGED:
      echo 'Suspecious, client IP changed when submit when form.';
      break;
  }
}
```

