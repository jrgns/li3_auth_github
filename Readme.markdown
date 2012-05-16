#GitHub authentication module for Lithium

This module adds "Login with GitHub" functionality to your lithium project. It uses the `Form` Auth adaptor and `Session` storage by default.

##Installation

Checkout the code to your libraries folder

    git clone https://github.com/jrgns/li3_auth_github

Include your library in your `app/config/bootstrap/libraries.php` file

    Libraries::add('li3_auth_github');

Ensure that both the Auth and the Session adapters are configured in your `app/config/bootstrap/session.php` file,
and that the file is included in `app/config/bootstrap.php`.

##Usage

The module adds three routes:

* `login/github`
* `callback/github`
* `login/github/requested`

To initiate the login process, take the user to `login/github`. If the login was successfuly, the user will be redirect to `/`.
