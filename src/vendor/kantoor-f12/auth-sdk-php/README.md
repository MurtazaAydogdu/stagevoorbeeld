# auth-sdk-php
The official php sdk for the auth server.

## Guidelines for SDK's

The following tables represents a guideline for Auth Server SDK's.

Not all calls are needed in each SDK.
SDK's that will only be used for back-end (PHP for instance) have no use for login calls. So those won't have to be implemented.

### Generic functions
These functions need to be implemented in all SDK's.

|Implemented| Priority | Function | Parameters | Description | 
|----------|----------|------------------------|---------------------------------|---------------------------------------------------------------------------------------------|
| :white_check_mark: | M | setOrigin | origin: string | sets origin on the SDK object, used for almost all calls. |
| :white_check_mark: | M | setAuthServerTokenPrivateKey | secret: string | sets setAuthServerTokenPrivateKey on the SDK object, used for `createAuthServerToken` call. |
| :white_check_mark: | M | setBaseUrl | url: string | sets HMACSignatureSecret on the SDK object, used to determine what authServer to use. |
| :white_check_mark: | M | createAuthServerToken | tokenPairApp (optional): string | create the auth server token which is needed for some calls with the auth server |
| :white_check_mark: | M | setAccessTokenPublicKey | key: string | sets public key to verify accessTokens with on the SDK object, used for `verifyAccessToken` call. |
| :white_check_mark: | M | verifyAccessToken | token: string | verifies if the given token is valid, returns the payload of the token |

### Login functions
These functions will only be useful for SDK's that'll be used in front-ends.

|Implemented| Priority | Function | Parameters | Description |
|----------|----------|-----------------------------|------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| :x: | M | login | provider: string | retrieve an accessToken + refreshToken retrieved by logging in using one of the available providers. |
| :x: | M | refresh | provider: string, refreshToken: string | refresh accessToken retrieved from login, use refreshToken. |
| :x: | S | isLoggedIn | - | check to determine whether the client is ready to make authenticated calls. |
| :x: | S | makeLoggedInCall | call: callable/promise | check if the user is logged in, if not try to refresh accessToken, if that fails throw error. if success make call with authorization options. |

### TokenPair functions
These functions will only be useful for SDK's that'll be used in back-ends.

|Implemented| Priority | Function | Parameters | Description |
|----------|----------|-----------------------------|------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| :white_check_mark:| M | createRetrieveTokenPairLink | provider: string | retrieve an url at which tokenPair retrieval can be made. |
| :white_check_mark:| M | refreshTokenPair | provider: string, tokenPairRefreshToken: string | refresh accessToken retrieved from tokenPair, use refreshToken. |
| :x:| S | isTokenPairLoggedIn | provider: string | check to determine whether the client is ready to make calls using the tokenPair. |
| :x:| S | makeTokenPairCall | call: callable/promise | check if the user is logged in, if not try to refresh accessToken, if that fails throw error. if success make call with authorization options. |
| :x:| S | setResourceServerTokenPublicKey | key: string | sets public key to verify resourceServerTokens with on the SDK object, used for `verifyResourceServerToken` call. |
| :x:| S | verifyResourceServerToken | token: string | verifies if the given token is valid |

### User functions
These functions will only be useful for SDK's that'll be used in back-ends.

|Implemented| Priority | Function | Parameters | Description |
|----------|----------|-----------------------------|------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| :x: | C | createUser | originId: integer, originRole: string, username: string, email: string | adds a new user to the auth server, used for login calls. |
| :x: | C | deleteUser | originId: integer | removes a user from the auth server. |
| :x: | C | changePassword | originId: integer, oldPassword: string, newPassword: string | updates the password of a user. |
| :x: | C | changeEmail | originId: integer, newEmail: string | updates the email of a user. |
| :x: | C | changeRole | originId: integer, newRole: string | updates the role of a user. |
| :x: | C | filterExistingUsers | originUsers: array (with integers) | check to see if certain users have an account in the auth server yet. |

### Info functions
These functions are extra, to provide additional information about the auth-server.

|Implemented| Priority | Function | Parameters | Description |
|----------|----------|-----------------------------|------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| :x:| C | getAvailableOrigins | - | retrieve origins configured on the auth server. |
| :x:| C | getAvailableProviders | type (optional): string | retrieve available providers on the auth server. |
| :x:| C | getVersion | - | retrieve the version of the auth server. |
| :x:| C | getEnvironment | - | retrieve the environment of the auth server. |

## Installing

This lib has not been added to Packagist and requires a bit more work to add to your application.
add the following to your package.json

```
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:kantoor-f12/auth-sdk-php.git"
    }
  ]
```

because this is a private lib you need to generate a personal access token for your application to install this package.
This can be found under settings -> Personal Access Tokens in github. Then add as follows.

```
$ composer config -g github-oauth.github.com $GITHUB_ACCESS_TOKEN
$ composer install
```


