# How to contribute to bpost API client library ?

## Report a bug

You found a bug? Don't panic, here are some steps to report it easily:

1. Search for it on [the bug tracker](https://github.com/Antidot-be/bpost-api-php-client/issues) (don't forget to use the search bar).
2. If you find a similar bug, don't hesitate to post a comment to add more importance to the related ticket.
3. If you didn't find it, [open a new ticket](https://github.com/Antidot-be/bpost-api-php-client/issues/new).

If you have to create a new ticket, try to apply the following advices:

- Give an explicit title to the ticket so it will be easier to find it later.
- Be as exhaustive as possible in the description: what did you do? What is the bug? What are the steps to reproduce the bug?
- We also need some information:
    + Your library branch (and the commit hash to be more precise)
    + Your server configuration: type of hosting, PHP, php-curl, php-mbstring versions

## Fix a bug

Did you want to fix a bug? To keep a great coordination between collaborators, you will have to follow these indications:

1. Be sure the bug is associated to a ticket and say you work on it.
2. [Fork this project repository](https://help.github.com/articles/fork-a-repo/).
3. [Create a new branch](https://help.github.com/articles/creating-and-deleting-branches-within-your-repository/). The name of the branch must be explicit and being prefixed by the related ticket id.
4. Make your changes to your fork and [send a pull request](https://help.github.com/articles/using-pull-requests/) on the **master branch**.

If you have to write code, please:
1. Follow the [PSR1/2](http://www.php-fig.org/psr/psr-2/).
2. Test your new classes/methods/lines.
3. For a bug, write the test which was absent (if your rollback the patch, this test will fail).

### How to test the library, and my changes ?

#### Build the library

In the workspace folder:
<pre><code>curl -sS https://getcomposer.org/installer | php
php composer.phar install --dev
</code></pre>

#### Execute the unit-tests

<pre><code>vendor/bin/phpunit
</code></pre>

After the unit tests execution, some reporting are generated :
- Unit test results: `tests/reports/phpunit.xml`
- Code coverage (XML format, for CI tools): `tests/reports/coverage-clover.xml`
- Code coverage (HTML format, for humans): `tests/reports/coverage-clover-html/index.html`

#### Execute the (not-unit) tests which call the API

Copy the file `tests/phpunit-credentials.php.dist` to `tests/phpunit-credentials.php` by editing the constants with your test account values.

Launch the specific tests:
<pre><code>vendor/bin/phpunit tests/connection-tests
</code></pre>
