# How to Upgrade Test Code from CI3 to CI4

## Table of Contents

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

- [Premise](#premise)
- [Setup PHPUnit](#setup-phpunit)
  - [phpunit.xml](#phpunitxml)
- [TestCase classes](#testcase-classes)
  - [DbTestCase](#dbtestcase)
  - [FeatureTestCase](#featuretestcase)
  - [Test Traits](#test-traits)
  - [setUp()](#setup)
- [Service Locator Config\Services](#service-locator-config%5Cservices)
- [reset_instance()](#reset_instance)
- [Controllers](#controllers)
  - [Send Request and Use Mocks](#send-request-and-use-mocks)
- [Create Mocks](#create-mocks)
  - [$this->getDouble()](#this-getdouble)
- [Monkey Patching](#monkey-patching)
- [Test Failures](#test-failures)
  - [Verify Method Invocation](#verify-method-invocation)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Premise

- You have test code with [ci-phpunit-test](https://github.com/kenjis/ci-phpunit-test) for your CodeIgniter3 application.
- You have upgraded the CodeIgniter3 application with *ci3-to-4-upgrade-helper*.

## Setup PHPUnit

### phpunit.xml

- Use the bootstrap file provided by *ci3-to-4-upgrade-helper*.
- If your test case file name suffixed `_test.php`, change the testsuite settings.

Example:
```diff
--- a/phpunit.xml.dist
+++ b/phpunit.xml.dist
@@ -1,6 +1,6 @@
 <?xml version="1.0" encoding="UTF-8"?>
 <phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
-       bootstrap="vendor/codeigniter4/framework/system/Test/bootstrap.php"
+       bootstrap="vendor/kenjis/ci3-to-4-upgrade-helper/src/CI3Compatible/Test/bootstrap.php"
        backupGlobals="false"
        colors="true"
        convertErrorsToExceptions="true"
@@ -29,6 +29,7 @@
    <testsuites>
        <testsuite name="App">
            <directory>./tests</directory>
+           <directory suffix="_test.php">./tests/app</directory>
        </testsuite>
    </testsuites>
    <logging>
```

## TestCase classes

*ci3-to-4-upgrade-helper* provides TestCase classes. 

The TestCase classes of *ci-phpunit-test* correspond to the following classes:
- `TestCase` → `Kenjis\CI3Compatible\Test\TestCase\TestCase`
- `DbTestCase` → `Kenjis\CI3Compatible\Test\TestCase\DbTestCase`
- `UnitTestCase` → `Kenjis\CI3Compatible\Test\TestCase\UnitTestCase`

### DbTestCase

If you want to use database seeding, use `DbTestCase`. And define the property `$seed`, `$seedOnce` and `$basePath` that you need. See <https://codeigniter4.github.io/CodeIgniter4/testing/database.html#migrations-and-seeds>.

### FeatureTestCase

*ci3-to-4-upgrade-helper* introduces a new TestCase that corresponds to CI4's [FeatureTestCase](https://codeigniter4.github.io/CodeIgniter4/testing/feature.html#the-test-class).

- `Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase`

If you use `$this->request`, use it instead of `TestCase`.

### Test Traits

*ci3-to-4-upgrade-helper* also provides Traits for testing.

- `Kenjis\CI3Compatible\Test\Traits\SessionTest`
  - Provides the mock session
- `Kenjis\CI3Compatible\Test\Traits\UnitTest`
  - Provides `$this->newController()`, `$this->newModel()`

### setUp()

If you call `setUp()` of PHPUnit, make sure to call `parent::setUp()`.

```php
     public function setUp(): void
     {
        parent::setUp();

        ...
     }
```

## Service Locator Config\Services

CI4's [`Config\Services`](https://codeigniter4.github.io/CodeIgniter4/concepts/services.html) is a service locator which provides instances of the CI4 framework class. It keeps shared instances including core classes.

The state of `Config\Services` may change test results. If you think you must reset the state, use `$this->resetServices()` that *ci3-to-4-upgrade-helper* provides.

## reset_instance()

`reset_instance()` is not needed now. If you need to reset, just use `$this->resetInstance()`.

## Controllers

### Send Request and Use Mocks

If you use `$this->request->setCallable()` or `$this->request->addCallable()`, add the `post_controller_constructor` *Event* in `app/Config/Events.php`.

```php
use Kenjis\CI3Compatible\Test\TestRequest;

Events::on('post_controller_constructor', function () {
    if (ENVIRONMENT === 'testing') {
        $testRequest = TestRequest::getInstance();

        $testRequest->runCallables();
    }
});
```

## Create Mocks

### $this->getDouble()

1. Install <https://github.com/kenjis/phpunit-helper>.
2. Update to namespaced classnames.

Example:
```php
$email = $this->getDouble('CI_Email', ['send' => true]);
```
↓
```php
use Kenjis\CI3Compatible\Library\CI_Email;

$email = $this->getDouble(CI_Email::class, ['send' => true]);
```

3. Disabling constructor may cause errors. If you get an error, try to set `true` in the third argument.

Example:
```php
$email = $this->getDouble(CI_Email::class, ['send' => true]);
```
↓
```php
$email = $this->getDouble(CI_Email::class, ['send' => true], true);
```

## Monkey Patching

1. Install <https://github.com/kenjis/monkey-patch>.
2. Copy `vendor/kenjis/ci3-to-4-upgrade-helper/src/CI3Compatible/Test/bootstrap.php` to `tests/bootstrap.php`
3. Configure Monkey Patch in `tests/bootstrap.php`.
4. Change the PHPUnit `bootstrap` file.

```diff
--- a/phpunit.xml.dist
+++ b/phpunit.xml.dist
@@ -1,6 +1,6 @@
 <?xml version="1.0" encoding="UTF-8"?>
 <phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
-  bootstrap="vendor/kenjis/ci3-to-4-upgrade-helper/src/CI3Compatible/Test/bootstrap.php"
+  bootstrap="tests/bootstrap.php"
   backupGlobals="false" colors="true"
   convertErrorsToExceptions="true"
   convertNoticesToExceptions="true"
```

5. To verify invocations, add `use MonkeyPatchTrait;` in your TestCase class.

Example:
```diff
--- a/tests/app/controllers/News_test.php
+++ b/tests/app/controllers/News_test.php
@@ -2,6 +2,7 @@
 
 use App\Database\Seeds\NewsSeeder;
 use Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase;
+use Kenjis\MonkeyPatch\Traits\MonkeyPatchTrait;
 
 /**
  * @group controller
@@ -9,6 +10,8 @@ use Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase;
  */
 class News_test extends FeatureTestCase
 {
+    use MonkeyPatchTrait;
+
     /**
      * Should run seeding only once?
      *
```

## Test Failures

### Verify Method Invocation

Verification of whether a method has been executed may fail as the default value of the parameter has been changed.

Example:
```diff
--- a/tests/app/models/News_model_with_mocks_test.php
+++ b/tests/app/models/News_model_with_mocks_test.php
@@ -128,9 +128,9 @@ class News_model_with_mocks_test extends TestCase
         $input->expects($this->any())->method('post')
             ->willReturnMap(
                 [
-                    // post($index = NULL, $xss_clean = NULL)
-                    ['title', null, 'News Title'],
-                    ['text',  null, 'News Text'],
+                    // post($index = NULL, $xss_clean = FALSE)
+                    ['title', false, 'News Title'],
+                    ['text',  false, 'News Text'],
                 ]
             );
 
```
