# Neos Flow essentials
I use this package to avoid having to recreate things that are needed in almost every app every time. Included are a UserService, a MailService (Fusion based mails), some settings and useful abstract PHP classes.

## Installation

Run
```
composer require neosrulez/neos-essentials
```
After installation run
```
flow flow:cache:flush --force
flow flow:package:rescan
```

## Configuration

```yaml
NeosRulez:
  Neos:
    Essentials:
      login:
        # These things are executed if the account is logged in when calling up the app
        ifAuthenticated:
          class: Acme\Package\Domain\Service\IsAuthenticated
          redirectToUri: /dashboard
        # These things are executed when the account has successfully logged out
        afterLogout:
          class: Acme\Package\Domain\Service\Logout
          redirectToUri: /homepage
        # These things are executed when the account has successfully logged in
        onAuthenticationSuccess:
          class: Acme\Package\Domain\Service\AuthSuccess
          redirectToUri: /dashboard
        # These things are executed when the login fails
        onAuthenticationFailure:
          class: Acme\Package\Domain\Service\AuthFailure
          redirectToUri: /homepage
      account:
        # Required!
        defaultRole: NeosRulez.Neos.Essentials:User
        # These things are done after an account is created. Account and password are passed in this function
        afterCreateAccount:
          class: Acme\Package\Domain\Service\UserService
      # Required! This is required to be able to use the getLoggedInUser() function from the UserService. The getLoggedInAccount() function is also available without this.
      user:
        model:
          class: Acme\Package\Domain\Model\User
        repository:
          class: Acme\Package\Domain\Repository\UserRepository
      # Required!
      mail:
        senderMail: noreply@foo.com
        senderName: Sender name

# You can safely override these settings if you need something else
Neos:
  Flow:
    security:
      authentication:
        providers:
          'NeosRulez.Neos.Essentials:Login':
            provider: 'PersistedUsernamePasswordProvider'
            entryPoint: 'WebRedirect'
    persistence:
      doctrine:
        filters:
          soft-deletable: 'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter'
        eventListeners:
          - events: [ 'onFlush', 'loadClassMetadata' ]
            listener: 'Gedmo\SoftDeleteable\SoftDeleteableListener'
          - events: [ 'prePersist', 'onFlush', 'loadClassMetadata' ]
            listener: 'Gedmo\Timestampable\TimestampableListener'

```

## Useful services

### `UserService.php`
The UserService covers the most important things that are needed in almost every app. Account and user creation, password generation, ability to check which user or account is logged in.

```php
use NeosRulez\Neos\Essentials\Service\UserService;

#[Flow\Inject]
protected UserService $userService;

$this->userService->createUser(string $email, string|null $password = null, string|null $role = null); # Create a new account and a new user. A separate user model abstracted from the AbstractUser.php model is required for this.
$this->userService->generatePassword(int $length); # Generates a secure password of any length
$this->userService->getLoggedInAccount(); # Returns the logged in account
$this->userService->getLoggedInUser(); # Returns the logged in user
```

### `MailService.php`
The MailService can send HTML mails with attachments using Swiftmailer.

```php
use NeosRulez\Neos\Essentials\Service\MailService;

#[Flow\Inject]
protected MailService $mailService;

$this->mailService()->sendMail(string $packageName, string $fusionPathAndFileName, array $variables, string $subject, string $sender, string $recipient, string|bool $replyTo = false, string|bool $cc = false, string|bool $bcc = false, array $attachments = []);

# Real life example
$this->mailService()->sendMail(
    'Acme.Package', # Package name for the Fusion Files
    'Acme/Package/Mail/FusionMail', # Component Name of the Fusion Files
    ['foo' => 'foos', 'bar' => $variable], # Variables used in the mail
    'Welcome to the Neos Flow application!', # Subject of the mail
    'noreply@foo.com', # Senders address
    $user->getEmail(), # Recipients address
    false, # In that case no reply to
    false, # In that case no cc
    false, # In that case no bcc
    ['/application/the_path_to_your_file1.pdf', '/application/the_path_to_your_file2.pdf'] # File paths to the files to be attached to the mail
);
```
#### `FusionMail.fusion`
```neosfusion
include: resource://Neos.Fusion/Private/Fusion/Root.fusion

Acme.Package.Mail.FusionMail = Neos.Fusion:Join {
    
    renderer = afx`
        <p>Nice that you use the app!</p>
        <p>
            <strong>Foo</strong> {foo}
            <strong>Bar</strong> {bar}
        </p>
    `
}
```

## Useful abstract PHP classes

### `AbstractModel.php`
This class provides things from the gedmo doctrine extension and a getter for the persistent object identifier and can be used to extend your own entity models.

```php
use NeosRulez\Neos\Essentials\Domain\Model\AbstractModel;

$entity->getIdentifier();

$entity->getCreated(); # Returns a DateTime Object
$entity->getCreated('Y-m-d'); # Returns a string
```

### `AbstractUser.php`
This class abstracts from the `AbstractModel.php` and provides an abstract class for your own user model, which contains setter and getter for account and setter and getter for the property "active".

```php
use NeosRulez\Neos\Essentials\Domain\Model\AbstractUser;

$userEntity->getAccount(); # Returns the Neos Flow Account referenced by the user
$userEntity->isActive(); # Returns a boolean
```

## Fusion page component
A page prototype to get an output easily.

```neosfusion
Acme.Package.StandardController.index = NeosRulez.Neos.Essentials:Page {

    htmlTag {
        lang = 'de'
    }

    content = Neos.Fusion:Component {

        renderer = afx`
            <div class="app">
                ...
            </div>
        `
    }
}
```

## Fusion login form component
The login component works out-of-the-box.

```neosfusion
prototype(Acme.Package:Component.LoginForm) < prototype(NeosRulez.Neos.Essentials:LoginForm) {

    username {
        label = 'Your username'
        placeholder = 'Enter your username ...'
    }

    loginButton {
        label = 'Login now!'
    }
}
```

## Author

* E-Mail: mail@patriceckhart.com
* URL: http://www.patriceckhart.com
