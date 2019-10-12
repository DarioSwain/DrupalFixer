# How to contribute

First of all please check out [this Rector "HowItWorks" guide](https://github.com/rectorphp/rector/blob/master/docs/HowItWorks.md) 
to better understand process how your code will be fixed.

Then follow next plan:

- Fork this repository.
- Implement test for your rector. (TDD)
- Implement rector itself.
- Run phpunit to check that your rector is working.
- Run phpcs and phpmd to check that code quality is in a good shape.
- Open pull request.
- That's it :)

## How to create a new rector

Let's assume that we're creating a simple Rector to fix [drupal_set_message issue in Drupal](http://hojtsy.hu/blog/2019-jul-30/prepare-drupal-9-stop-using-drupalsetmessage?fbclid=IwAR2GbT_GugnotzvgqoIwly3vvAiDXXOMsMmSml7jpuRQf_qfIXin_mkbzkg).

First of all you should create a new Rector class in src/Rector/D80, let's call it SetMessageRector.

```
<?php

namespace DS\DrupalFixer\Rector\D80;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\ConfiguredCodeSample;
use Rector\RectorDefinition\RectorDefinition;

final class SetMessageRector extends AbstractRector
{
    /** @var array */
    private $rectorConfiguration = [];

    /** @param array $rectorConfiguration e.g. ["drupal_set_message" => ["Drupal", "messenger"]] */
    public function __construct(array $rectorConfiguration = [])
    {
        $this->rectorConfiguration = $rectorConfiguration;
    }

    /** @return RectorDefinition */
    public function getDefinition(): RectorDefinition
    {
        //TODO: Implement definition. 
    }

    /** @return string[] */
    public function getNodeTypes(): array
    {
        //TODO: Implement getNodeTypes.
    }

    /** @param Node $node */
    public function refactor(Node $node): ?Node
    {
        //TODO: Main refactoring logic will be putted here.
    }
}
```

Currently our Rector is mostly do nothing. Let's add Rector definition to it.

Please check [RectorDefinition class](https://github.com/rectorphp/rector/blob/master/src/RectorDefinition/RectorDefinition.php)
 fore more information. Basically you just need to describe what your rector is doing and provide 
 [ConfiguredCodeSample](https://github.com/rectorphp/rector/blob/master/src/RectorDefinition/ConfiguredCodeSample.php) with 
 code examples how it works.
 
 Here is an example of our Rector Definition:
```
...

public function getDefinition(): RectorDefinition
{
    return new RectorDefinition(
        'Turns drupal_set_message to Messenger API.', 
        [
            new ConfiguredCodeSample(
                'drupal_set_message("...", "...");',
                '\Drupal::messenger()->addMessage("...", "...");',
                []
            ),
        ]
    );
}

...

```

Now we need to setup which nodes will be passed into your Rector, you can do this via ```getNodeTypes``` method.

Literally you need to specify array of Node classes which you would like to catch via your Rector.

In [this Rector documentation](https://github.com/rectorphp/rector/blob/master/docs/NodesOverview.md) you can find all supported Node types.

```
...
/** @return string[] */
public function getNodeTypes(): array
{
    // In our example we need to find all function calls. like drupal_set_message(), implode(), strlen(), etc... 
    return [FuncCall::class];
}
...

```

Our final step is to implement refactoring logic, refactor method is used for such purpose.

This method has one input parameter which is Node. Node of type which you've specified in ```getNodeTypes``` method.

Please read comments in example to better understand what is going om here:
```
...
/** @param Node $node */
public function refactor(Node $node): ?Node
{
    // We are checking that provided node is of FuncCall type. In our example it's not required to do, because we're catching only FuncCall nodes.
    if (!$node instanceof FuncCall) {
        // Returning null means that we're not doing anythig with provided Node.
        return null;
    }

    // Checking that it's not an anonymous function.
    if (!$node->name instanceof Name) {
        return null;
    }

    // Extracting function name
    $functionName = $this->getName($node);
    
    // Checking that function name is equal to 'drupal_set_message'
    if ($functionName !== 'drupal_set_message') {
        return null;
    }

    // Replace current node with new one created by createMethodCall method call.
    // Resulted node will be converted to: (object)->addMessage(paramFromOriginalNode1, paramFromOriginalNode2)
    return $this->createMethodCall(
        // Create new static method call with empty arguments. e.g \Drupal::messenger();
        $this->createStaticCall('Drupal', 'messenger', []),
        'addMessage',
        $node->args
    );
}
...
```

In the end you'll get Rector as described [here](/examples/SetMessageRector.php).

That's it our custom Rector is ready. Now let's check how to cover it with unit testing.

## How to create test for your new rector

First of all create a directory for your rector test. Go to /tests/Rector/D80 and create a directory with the same name 
as your Rector class name. (SetMessageRector from example above).

In this new "SetMessageRector" directory you should create sub-directory Fixture - here our test fixtures will be located and 
entire test class, please call it in the same way as your Rector name + Test postfix, for our example it will be ```SetMessageRectorTest.php```

So let's define fixtures first, to do this please create your fixture file, let's call it ```fixture.php.inc```:

```
<?php

function drupal_set_message($a, $b) {};

drupal_set_message('Hello world', 'custom');

?>
-----
<?php

function drupal_set_message($a, $b) {};

\Drupal::messenger()->addMessage('Hello world', 'custom');

?>

```

Part before "-----" treated as a file which you're providing to Rector as Input. Part after is an Output
 which you're expecting to receive.
 
Note: Input code snippet will be checked via PHPStan which is loading it in user-land, so all unknown functions have to be defined, 
otherwise you'll see PHP fatal error that your function not found.

Now you're just need to add ```SetMessageRectorTest.php```:
```
<?php declare(strict_types=1);

namespace DS\DrupalFixer\Tests\Rector\D80\SetMessageRector;

use DS\DrupalFixer\Rector\D80\SetMessageRector;
use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class SetMessageRectorTest extends AbstractRectorTestCase
{
    /**
     * @param string $file
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        // Path to your input texture will be passed here from provideDataForTest() data provider.
        $this->doTestFile($file);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc'];
    }

    /**
     * @return mixed[]
     */
    protected function getRectorsWithConfiguration(): array
    {
        // Here you can provide custom configuration for your Rector.
        return [
            SetMessageRector::class => []
        ];
    }
}
```

So that's it! You can run ```vendor/bin/phpunit``` from project root directory and find out the results.
