Installation
=============

You can use the CultuurNet/Auth PHP library in different ways:

* Standalone. Clone or download from github and use [Composer](http://getcomposer.org). Run ``composer install`` from
  the root of the clone to download the necessary dependencies. Standalone usage is probably only useful for testing
  purposes.
* Inside your project: require the cultuurnet/auth package (it is
  [registered on Packagist](https://packagist.org/packages/cultuurnet/auth)) in your project's composer.json file.

```json
{
    "require": {
        "cultuurnet/auth": "dev-master"
    }
}
```

Command line tool
===================

The included command line tool is intended for testing and debugging.

Installation
------------
The command line tool requires some additional third-party PHP libraries to do the heavy lifting
(among which the [Symfony Console Component](http://symfony.com/doc/current/components/console/introduction.html)).
You can also also easily download them with Composer by running ``composer install --dev``.

When using CultuurNet\Auth standalone, as a download or clone from github, the command line tool is available at bin/cultuurnet-auth.

When included inside your own project by using Composer, the command line tool is available at
vendor/bin/cultuurnet-auth. Further examples in this documentation will use that location.

Listing available commands and getting help
-------------------------------------------

Use the ``list`` command to get a list of available commands:

    $ ./vendor/bin/cultuurnet-auth list

Use the ``help`` command to get help for a specific command, for example for the ``authorize`` command:

    $ ./vendor/bin/cultuurnet-auth help authorize

User preferences
----------------

The command line tool makes use of user preferences which you can define in an INI file auth.ini inside a .cultuurnet
directory located in your HOME directory: ~/.cultuurnet/auth.ini.
Preferences in this file are used as defaults, and can be overwritten per request by several options of the command line
 tool.

The directives that can be used in the INI file are:

* endpoint: base url of the CultuurNet OAuth authentication endpoint, for example http://test.uitid.be/culturefeed/rest.
* consumer-key: the OAuth consumer key
* consumer-secret: the OAuth consumer secret

Authorization
-------------

Use the ``authorize`` command to authenticate with UiTiD and authorize a service consumer to access the different
CultuurNet webservices on behalf of you.

To authenticate with UiTiD, you need to specify your username and password. The command line tool
retrieves an OAuth request token, posts your username and password to the UiTiD website, authorizes the consumer
there as well, intercepts the returned OAuth verifier, and finally exchanges the temporary credentials for access
token credentials.

    $ ./vendor/bin/cultuurnet-auth --consumer-key=76163fc774cb42246d9de37cadeece8a --consumer-secret=fff975c5a8c7ba19ce92969c1879b211 --username=foo --password=bar authorize

For added security you can input username and password interactively instead of using command line options.

    $ ./vendor/bin/cultuurnet-auth --consumer-key=76163fc774cb42246d9de37cadeece8a --consumer-secret=fff975c5a8c7ba19ce92969c1879b211 authorize
    User name: foo
    Password:

You can retrieve and/or store commonly necessary configuration data & credentials for authorized communication with the
different CultuurNet web services in a so-called 'session' file, by specifying the desired location with the ``--file`` option.

You can continue to use the session file in other commands by specifying the ``--file`` option again, so you do not to have to
specify all command line options over and over again.

Please ensure the file is located at a safe place and not accessible by other users, as it will allow others
to make requests on your behalf. The following items are maintained in a session file:

* endpoint
* consumer key & consumer secret
* access token & token secret retrieved by the ``authorize`` command
