# m-commander
Easy tool for executing your own module via command line

1.Install via Composer (best option, however you can use it without it)

```
"200mph/m-commander": "1.*"
```

2.Create your command line module class and extend AbstractCliModule() from m-commander vendor

```
namespace cli\MyTest;

use m-commander\AbstractCliModule;

class TestMe() extends AbstractCliModule 
{

    /**
     * We have to create execute() method (abstraction requirements)
     *
     * @return void
    /*
    protected function execute()
    {

        $this->successOutput('Hello World' . PHP_EOL);

    }
}
```

3.Run your module

```
./vendor/bin/m-commander cli\\MyTest\\TestMe -v
```

You can also use semi quotes to avoid double back slashes notation.

```
./vendor/bin/m-commander 'cli\MyTest\TestMe' -v
```

Above notation is recommended if command have to be executed in CRON, or another shell script.

For more examples please have a look in to ./examples folder
